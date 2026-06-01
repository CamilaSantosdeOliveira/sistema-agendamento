<?php
/**
 * Model para Agendamentos
 * Sistema de Agendamento
 */

require_once '../config/database.php';

class Agendamento {
    private $conn;
    private $table = 'agendamentos';

    // Propriedades
    public $id;
    public $nome;
    public $email;
    public $data;
    public $hora;
    public $servico;
    public $status;
    public $criado_em;
    public $atualizado_em;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    /**
     * Cria um novo agendamento
     */
    public function criar() {
        $query = "INSERT INTO " . $this->table . " 
                  SET nome = :nome, 
                      email = :email, 
                      data = :data, 
                      hora = :hora, 
                      servico = :servico, 
                      status = :status,
                      criado_em = NOW()";

        $stmt = $this->conn->prepare($query);

        // Sanitiza os dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->data = htmlspecialchars(strip_tags($this->data));
        $this->hora = htmlspecialchars(strip_tags($this->hora));
        $this->servico = htmlspecialchars(strip_tags($this->servico));
        $this->status = 'agendado';

        // Bind dos parâmetros
        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':data', $this->data);
        $stmt->bindParam(':hora', $this->hora);
        $stmt->bindParam(':servico', $this->servico);
        $stmt->bindParam(':status', $this->status);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Lista todos os agendamentos
     */
    public function listar() {
        $query = "SELECT * FROM " . $this->table . " 
                  ORDER BY data ASC, hora ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Busca agendamento por ID
     */
    public function buscarPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Atualiza um agendamento
     */
    public function atualizar() {
        $query = "UPDATE " . $this->table . " 
                  SET nome = :nome, 
                      email = :email, 
                      data = :data, 
                      hora = :hora, 
                      servico = :servico, 
                      status = :status,
                      atualizado_em = NOW()
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitiza os dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->data = htmlspecialchars(strip_tags($this->data));
        $this->hora = htmlspecialchars(strip_tags($this->hora));
        $this->servico = htmlspecialchars(strip_tags($this->servico));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind dos parâmetros
        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':data', $this->data);
        $stmt->bindParam(':hora', $this->hora);
        $stmt->bindParam(':servico', $this->servico);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * Deleta um agendamento
     */
    public function deletar($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * Verifica se já existe agendamento para data/hora
     */
    public function verificarDisponibilidade($data, $hora, $id_excluir = null) {
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE data = :data AND hora = :hora AND status != 'cancelado'";
        
        if ($id_excluir) {
            $query .= " AND id != :id_excluir";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':hora', $hora);
        
        if ($id_excluir) {
            $stmt->bindParam(':id_excluir', $id_excluir);
        }

        $stmt->execute();

        return $stmt->rowCount() == 0;
    }

    /**
     * Busca agendamentos por data
     */
    public function buscarPorData($data) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE data = :data 
                  ORDER BY hora ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':data', $data);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Busca agendamentos por email
     */
    public function buscarPorEmail($email) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE email = :email 
                  ORDER BY data DESC, hora DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
?>
