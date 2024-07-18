<?php
class Tabela{

  /**
  * Instancia da tabela
  * @var string
  */
  private $tabela;


  /**
  * Construtor responsável por definir os valores das propriedades da tabela
  * @param string $tabela  Nome da tabela no banco de dados
  */
  function __construct($tabela = null){
    $this->tabela = $tabela;
  }


  /**
  * Método responsavel pela conexao do banco de dados
  * @return object Retorna um objeto de PDO
  */
  public function conectar(){
    $host           = 'localhost';
    $nomeBancoDados = 'senai';
    $usuario        = 'root';
    $senha          = '';

    $dsn = 'mysql:host='.$host.';dbname='.$nomeBancoDados;

    try {
      $pdo = new PDO($dsn, $usuario, $senha);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $pdo;
    } catch (PDOException $e) {
      echo "Erro na conexão: " . $e->getMessage();
    }
  }

  /**
  * Método eu gero o contexto do insert e chamo o execute para refletir ao banco de dados
  * @param  array  $dados        Dados a serem inseridos
  * @return bool Retorna true or false para o status do comando
  */
  public function insert($dados = []){
    //INDICE DOS DADOS QUE REPRESENTA AS COLUNAS
    $colunas = array_keys($dados);

    //MONTO OS PLACEHOLDERS
    $placeholders = $this->getPlaceholders($dados);

    // QUERY COM PLACEHOLDERS
    $query = "INSERT INTO ".$this->tabela." (".implode(', ', $colunas).") VALUES (".implode(', ', $placeholders).")";

    return $this->execute($query, $dados);
  }

  /**
  * Método monta o select e chama a query
  * @param  string $condicao           Condição para a SQL
  * @return mysqli Retorno da consulta
  */
  public function select($condicao = null){
    //CONDICAO DA SQL CASO TENHA
    if(!is_null($condicao)) $condicao = 'WHERE '.$condicao;

    $query = "SELECT * FROM ".$this->tabela." ".$condicao;
    return $this->query($query);
  }

  /**
   * Método responsavel por gerar o update no banco de dados
   * @param  array  $dados             Dados a serem refletidos no banco
   * @param  string $condicao          Condicional para o update
   * @return bool Retorna true or false para o status do comando
   */
  public function update($dados = [], $condicao = null){
    //PLACEHOLDER QUE IRÃO SER INSERIDOS
    $placeholders = $this->getPlaceholders($dados, 'duplo');

    // QUERY COM PLACEHOLDERS
    $query = "UPDATE ".$this->tabela." SET ".implode(',', $placeholders);
    if(!is_null($condicao)) $query .= ' WHERE '.$condicao;

    return $this->execute($query, $dados);
  }


  /**
   * Método para remover dados do banco de dados
   * @param  string $condicao      Condição para o delete
   * @return bool       Retorna true or false para o comando solicitado
   */
  public function delete($condicao = null){
    if(!is_null($condicao)){
      $query = "DELETE FROM ".$this->tabela." WHERE ".$condicao;
      return $this->execute($query);
    }
  }


  /**
  * Método gera os placeholders de acordo com os dados passado
  * @param  array  $dados         Dados passados
  * @return array Retorna os placeholders em array
  */
  public function getPlaceholders($dados = [], $tipo = 'simples'){
    $retorno = [];
    foreach ($dados as $key => $value) {
      if($tipo == 'simples'){
        $retorno[] = ':'.$key;
      }else{
        $retorno[] = $key.' = :'.$key;
      }
    }
    return $retorno;
  }


  /**
  * Método para executar e refletir no banco de dados
  * @param  string $query             Query preparada
  * @param  array  $dados              Dados a serem manipulados na query
  * @return mysqli Retorna do mysql
  */
  public function execute($query = null, $dados = []){
    try {
      //CONECTO NO BANCO
      $pdo = $this->conectar();

      //PREPARA A QUERY
      $stmt = $pdo->prepare($query);

      //ASSOCIAÇÃO DOS VALORES AOS PLACEHOLDERS
      foreach ($dados as $key => $value) {
        $stmt->bindValue(':'.$key, $value);
      }

      //EXECUTA A QUERY
      return $stmt->execute();
    } catch (PDOException $e) {
      echo "Erro ao inserir dados: " . $e->getMessage();
    }
  }

  /**
  * Método para executar queries SELECT
  * @param  string $query             Query preparada
  * @return mysqli Retorno da consulta
  */
  public function query($query) {
    try {
      // CONECTA NO BANCO
      $pdo = $this->conectar();

      return $pdo->query($query);

    } catch (PDOException $e) {
      echo "Erro ao consultar dados: " . $e->getMessage();
      return false;
    }
  }



}
