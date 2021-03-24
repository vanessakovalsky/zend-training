<?php
namespace User\Model;

use User\Model\User;
use RuntimeException;
use Laminas\Db\Adapter\Adapter;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\TableGateway\TableGatewayInterface;

class UserTable extends AbstractTableGateway
{
    protected $adapter;
    protected $table = 'user';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    // public function fetchAll()
    // {
    //     return $this->tableGateway->select();
    // }

    public function save(array $data){
        $values = [
            'username' => ucfirst($data['username']),
            'email'    => mb_strtolower($data['email']),
            'password' => (new Bcrypt())->create($data['password'])
        ];

        $sqlQuery =  $this->sql->insert()->values($values);
        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function findOneByEmail(string $email)
    {
        $sqlQuery = $this->sql->select()
        ->where(['email' => $email]);
        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
        $row = $sqlStmt->execute()->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $email
            ));
        }
        $userAccount = new User();
        $userAccount->exchangeArray($row);

        return $userAccount;
    }

   


    /**
     * Get the value of table
     */ 
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Set the value of table
     *
     * @return  self
     */ 
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }
}

