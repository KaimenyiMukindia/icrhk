<?php declare(strict_types=1);

namespace App\Models\WordPress;

use Illuminate\Database\Eloquent\Model;

abstract class WordPressModel extends Model
{
    protected $connection = 'wordpress';

    public function getTable(): string
    {
        $prefix = config('database.connections.wordpress.prefix', 'wp_');
        return $prefix . parent::getTable();
    }
}
