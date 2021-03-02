<?php
namespace Andriichello\Types;

class User implements IdentifiableDbModelInterface
{
    public int $id;
    public string $login;
    public string $password;

    public static function create(int $id, string $login, string $password): static {
        $obj = new User();

        $obj->id = $id;
        $obj->login = $login;
        $obj->password = $password;
        return $obj;
    }

    public function identify()
    {
        return $this->id;
    }
}