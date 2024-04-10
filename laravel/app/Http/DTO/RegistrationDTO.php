<?php

namespace App\Http\DTO;

class RegistrationDTO
{
    public string $name;
    public string $last_name;
    public string $email;
    public string $password;

    /**
     * Constructor to initialize the properties with provided values
     * @param string $name
     * @param string $last_name
     * @param string $email
     * @param string $password
     */
    public function __construct(string $name, string $last_name, string $email, string $password)
    {
        $this->name = $name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->password = $password;
    }
}
