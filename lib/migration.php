<?php


namespace Adv\Migrations;


use Adv\Duplicates\Messages\Forum;

class Migration
{
    protected $users;
    protected $disk;

    public function __construct()
    {
        $this->users = Includer::getMigrationUsers();
        $this->disk = new Disk;
    }

    public function run()
    {
        foreach ($this->users as $newUser => $oldUser) {
            Tasks::updateTasks($oldUser, $newUser);
            Forum::changeMessageAuthor($oldUser, $newUser);
            $this->disk->setRightsToDiskObjects($oldUser, $newUser);
        }
    }
}