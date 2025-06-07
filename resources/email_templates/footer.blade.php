@php
$template = \src\Libs\EmailTemplate::record(\src\Models\MailTemplate::FOOTER);
if ($template !== false) {
    echo $template['mte_content'];
}
@endphp