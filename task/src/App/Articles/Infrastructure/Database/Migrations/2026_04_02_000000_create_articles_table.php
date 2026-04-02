<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Unikalny identyfikator artykułu (UUID).');
            $table->string('title')->comment('Tytuł artykułu widoczny na stronie (nagłówek H1 / treść).');
            $table->string('slug')->unique()->comment('Fragment adresu URL; unikalny, stabilny identyfikator strony artykułu.');
            $table->text('excerpt')->nullable()->comment('Krótki lead / zajawka wyświetlana na listach i w podglądach.');
            $table->longText('body')->comment('Pełna treść artykułu (np. HTML lub Markdown wg konwencji aplikacji).');
            $table->string('status')->default('draft')->comment('Stan publikacji (np. draft, published); steruje widocznością i workflow.');
            $table->string('category')->comment('Kategoria lub typ artykułu (etykieta biznesowa / nawigacja).');
            $table->uuid('application_manager_id')->nullable()->comment('Powiązanie z rekordem aplikacji w Application Manager (np. multi-tenant); opcjonalne.');
            $table->foreign('application_manager_id')
                ->references('id')
                ->on('applications')
                ->nullOnDelete();
            $table->string('author')->comment('Autor artykułu (nazwa lub identyfikator tekstowy wg wymagań produktu).');
            $table->dateTime('published_at')->nullable()->comment('Data i czas publikacji; null oznacza brak publikacji / harmonogram.');

            $table->string('meta_title')->nullable()->comment('Tytuł pod SEO i tag <title>; gdy pusty, można zastąpić polem title.');
            $table->text('meta_description')->nullable()->comment('Meta description dla wyników wyszukiwania (krótki opis strony).');
            $table->text('canonical_url')->nullable()->comment('Pełny adres kanoniczny (unikanie duplikatów treści w indeksie).');
            $table->string('og_title')->nullable()->comment('Tytuł Open Graph przy udostępnianiu w social media.');
            $table->text('og_description')->nullable()->comment('Opis Open Graph przy udostępnianiu w social media.');
            $table->text('og_image_url')->nullable()->comment('URL obrazu Open Graph (podgląd karty przy udostępnianiu).');
            $table->string('robots')->nullable()->comment('Dyrektywy robots (np. index,follow lub noindex,nofollow) dla robotów indeksujących.');

            $table->timestamp('created_at')->nullable()->comment('Data i czas utworzenia rekordu w bazie.');
            $table->timestamp('updated_at')->nullable()->comment('Data i czas ostatniej modyfikacji rekordu w bazie.');

            $table->index('application_manager_id');
            $table->index('published_at');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
