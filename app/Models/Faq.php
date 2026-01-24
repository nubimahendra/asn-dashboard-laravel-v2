<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'keywords',
        'answer',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include FAQs matching the keywords in the message.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $message
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindByKeyword(Builder $query, string $message): Builder
    {
        // Normalisasi pesan: lowercase
        $message = strtolower($message);

        return $query->where('is_active', true)
            ->where(function ($q) use ($message) {
                // Mencari apakah 'keywords' ada di dalam $message
                $words = explode(' ', $message);
                foreach ($words as $word) {
                    $word = trim($word);
                    if (strlen($word) > 2) {
                        $q->orWhere('keywords', 'LIKE', "%{$word}%");
                    }
                }
            });
    }

    /**
     * Helper logic if preferred to look up answer directly or return default.
     * 
     * @param string $message
     * @return string|null
     */
    public static function findAnswerOrNull(string $message): ?string
    {
        $faq = self::findByKeyword($message)->first();
        return $faq ? $faq->answer : null;
    }
}
