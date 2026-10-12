<?php

class Seed_default_users {

    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->database();
    }

    public function up()
    {
        $users = [
            [
                'username'  => 'admin',
                'email'     => 'admin@example.com',
                'password'  => 'admin123',
                'role'      => 'admin',
                'is_active' => 1,
            ],
            [
                'username'  => 'viewer',
                'email'     => 'viewer@example.com',
                'password'  => 'viewer123',
                'role'      => 'user',
                'is_active' => 1,
            ],
        ];

        foreach ($users as $user) {
            $existing = $this->_lava->db
                ->table('users')
                ->where('username', $user['username'])
                ->get();

            if (!$existing) {
                $this->_lava->db->table('users')->insert($user);
            }
        }
    }

    public function down()
    {
        $this->_lava->db
            ->table('users')
            ->where_in('username', ['admin', 'viewer'])
            ->delete();
    }
}
