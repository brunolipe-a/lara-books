<x-mail::message>
    # Parabéns

    Meus parabéns {{ $userData['name'] }}. Nesse ano você leu {{ $userData['books_counter'] }} livros e
    {{ $userData['pages_counter'] }} páginas.

    Obrigado,
    {{ config('app.name') }}
</x-mail::message>
