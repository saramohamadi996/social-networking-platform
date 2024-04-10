<?php

namespace App\Http\DTO;

class UserDTO
{
    public string $name;
    public string $last_name;
    public string $email;

    /**
     * UserDTO constructor.
     * @param string $name
     * @param string $last_name
     * @param string $email
     */
    public function __construct(string $name, string $last_name, string $email)
    {
        $this->name = $name;
        $this->last_name = $last_name;
        $this->email = $email;
    }
}
