<?php

namespace Nadi\CodeIgniter\Data;

use Nadi\CodeIgniter\Concerns\InteractsWithMetric;
use Nadi\Data\Entry as DataEntry;
use Throwable;

class Entry extends DataEntry
{
    use InteractsWithMetric;

    public $user;

    public function __construct($type, array $content, $uuid = null)
    {
        parent::__construct($type, $content, $uuid);

        $this->registerMetrics();

        try {
            if (function_exists('auth') && auth()->loggedIn()) {
                $this->user(auth()->user());
            }
        } catch (Throwable $e) {
            // Do nothing.
        }
    }

    public function user($user): static
    {
        $this->user = $user;

        $id = method_exists($user, 'getAuthId') ? $user->getAuthId() : ($user->id ?? null);
        $name = $user->username ?? null;
        $email = $user->email ?? null;

        $this->content = array_merge($this->content, [
            'user' => [
                'id' => $id,
                'name' => $name,
                'email' => $email,
            ],
        ]);

        $this->tags(['Auth:'.$id]);

        return $this;
    }
}
