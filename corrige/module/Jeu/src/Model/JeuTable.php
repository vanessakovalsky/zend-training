<?php
namespace Jeu\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class JeuTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function getJeu($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    public function saveJeu(Jeu $jeu)
    {
        $data = [
            'editor' => $jeu->editor,
            'title'  => $jeu->title,
        ];

        $id = (int) $jeu->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getJeu($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update jeu with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteJeu($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}

