<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $subject
 * @property string $descr
 * @property string $content
 * @property string $attached_files
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @mixin Eloquent
 */

class Notifications extends Model
{
    protected $table = 'mail_notification';

    protected $fillable = [
        'user_id', 'fullname', 'title', 'subject', 'body', 'attached_files'
    ];
}
