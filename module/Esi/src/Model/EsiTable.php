<?php

namespace Esi\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

class EsiTable
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

    public function getEsi($id)
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

    public function saveEsi(Esi $esi)
    {
        $data = [
            'titre' => $esi->titre,
            'annee'  => $esi->annee,
            'auteur'  => $esi->auteur,
            'resume'  => $esi->resume,
        ];

        $id = (int) $esi->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getEsi($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update bibliotheque with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteEsi($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}

 ?>
