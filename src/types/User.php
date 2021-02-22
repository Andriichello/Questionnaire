<?php

namespace Andriichello\types;

class User {
    protected int $id;
    protected string $login;
    protected string $password;

    /**
     * User constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        if (empty($params)) {
            $this->id = -1;
            $this->login = '';
            $this->password = '';
        } else {
            $this->id = empty($params['id']) ? -1 : $params['id'];
            $this->login = empty($params['login']) ? '' : $params['login'];
            $this->password = empty($params['password']) ? '' : $params['password'];
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }



}