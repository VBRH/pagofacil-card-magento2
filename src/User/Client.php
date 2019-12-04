<?php

declare(strict_types=1);

namespace PagoFacil\Payment\Source\User;

use PagoFacil\Payment\Source\Client\EndPoint;

class Client
{
    /** @var string $idUser  */
    private $idUser = null;
    /** @var string $idBranchOffice */
    private $idBranchOffice = null;
    /** @var string $passPhrase */
    private $passPhrase = null;
    /** @var EndPoint $endpoint */
    private $endpoint = null;

    /**
     * Client constructor.
     * @param string $idUser
     * @param string $idBranchOffice
     * @param string $passPhrase
     * @param EndPoint $endpoint
     */
    public function __construct(
        string $idUser,
        string $idBranchOffice,
        string $passPhrase,
        EndPoint $endpoint
    ) {
        $this->idUser = $idUser;
        $this->idBranchOffice = $idBranchOffice;
        $this->passPhrase = $passPhrase;
        $this->endpoint = $endpoint;
    }

    /**
     * @return string
     */
    public function getIdUser(): string
    {
        return $this->idUser;
    }

    /**
     * @return string
     */
    public function getIdBranchOffice(): string
    {
        return $this->idBranchOffice;
    }

    /**
     * @return string
     */
    public function getPassPhrase(): string
    {
        return $this->passPhrase;
    }

    /**
     * @return EndPoint
     */
    public function getEndpoint(): EndPoint
    {
        return $this->endpoint;
    }
}