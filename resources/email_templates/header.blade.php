@php
    $template = \src\Libs\EmailTemplate::record(\src\Models\MailTemplate::HEADER);
    if ($template !== false) {
        $template['mte_content'] = str_replace(
            '<figure', '<figure style="margin: 0;"', $template['mte_content']
        );
        $template['mte_content'] = str_replace(
            '<img', '<img style="width: 100%;"', $template['mte_content']
        );
    }
@endphp

@if ($template !== false)
    {!! $template['mte_content'] !!}
@endif
