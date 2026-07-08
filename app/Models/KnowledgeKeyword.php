<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeKeyword extends Model
{
    protected $fillable = ['bot_knowledge_id', 'kata_kunci'];
    public $timestamps = true;

    public function botKnowledge(): BelongsTo
    {
        return $this->belongsTo(BotKnowledge::class);
    }
}
