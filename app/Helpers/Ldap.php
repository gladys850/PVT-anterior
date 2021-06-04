<?php

namespace App\Helpers;

use Adldap;

class Ldap
{
    private $config;
    private $connection;

    public function __construct()
    {
        $this->config = array(
            'hosts' => [env("LDAP_HOST")],
            'port' => env("LDAP_PORT", 389),
            'username' => env("LDAP_ADMIN_USERNAME"),
            'password' => env("LDAP_ADMIN_PASSWORD"),
            'base_dn' => env("LDAP_BASEDN"),
            'timeout' => env("LDAP_TIMEOUT", 6),
            'schema' => Adldap\Schemas\OpenLDAP::class
        );
        $this->config['username'] = env("LDAP_ADMIN_PREFIX") . "=" . $this->config['username'] . "," . $this->config['base_dn'];
        $ad = new Adldap\Adldap();
        $ad->addProvider($this->config);
        $this->connection = $ad->connect();
        $this->config['host'] = $this->config['hosts'][0];
        $this->config['user_id_key'] = env("LDAP_ACCOUNT_PREFIX", "uid");
        $this->config['group'] = env("LDAP_ACCOUNT_SUFFIX", null);
    }

    public function verify_open_port()
    {
        return @fsockopen($this->config['host'], $this->config['port'], $errno, $errstr, $this->config['timeout']);
    }

    protected function is_connected()
    {
        $connected = $this->connection && $this->verify_open_port();
        if ($connected) {
            return true;
        }
        abort(500, 'Error en la conexión con el servidor LDAP');
    }

    public function get_config()
    {
        return $this->config;
    }

    public function __get($connection)
    {
        return $this->connection;
    }

    private function username($user)
    {
        $dn = $this->config['user_id_key'] . '=' . $user . ',';
        if ($this->config['group']) $dn .= $this->config['group'] . ',';
        $dn .= $this->config['base_dn'];
        return $dn;
    }

    private function identifier($type)
    {
        if ($type == 'id') {
            return 'employeeNumber';
        } else {
            return $type;
        }
    }

    public function bind($username, $password)
    {
        $this->is_connected();
        return $this->connection->auth()->attempt($this->username($username), $password);
    }

    private function entry_exists($id, $type = 'id')
    {
        $this->is_connected();
        $user = $this->connection->search()->findBy($this->identifier($type), $id);
        if ($user) {
            return true;
        } else {
            return false;
        }
    }

    private function format_attributes($user)
    {
        $user = $user->getAttributes();
        foreach ($user as $key => $attributes) {
            if (is_int($key) || in_array($key, ['objectclass', 'ou', 'userpassword', 'dn'])) {
                unset($user[$key]);
            } else {
                $user[$key] = $attributes[0];
            }
        }
        return $user;
    }

    public function get_entry($id, $type = 'id', $format = true)
    {
        $this->is_connected();
        $user = $this->connection->search()->findBy($this->identifier($type), $id);
        if ($user && $format) {
            $user = $this->format_attributes($user);
        }
        return $user;
    }

    public function get_entries()
    {
        $this->is_connected();
        $users = $this->connection->search()->whereHas('uid')->get();
        foreach ($users as $key => $user) {
            $users[$key] = $this->format_attributes($user);
        }
        $users = $users->all();
        usort($users, function ($a, $b) {
            return $a['sn'] <=> $b['sn'];
        });
        return $users;
    }
}