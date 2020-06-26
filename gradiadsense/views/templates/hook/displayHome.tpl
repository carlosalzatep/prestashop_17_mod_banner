
{if $GRADIADSENSE_ACTIVO }
<section id="home-banner" style="background-image: url('{$GRADIADSENSE_BACKGROUND}');">
    <h3>{$GRADIADSENSE_TITULO|escape:'html'}</h3>
    <a id="cta_btn" href="{$GRADIADSENSE_CTA_URL}" class="btn btn-primary">
        {$GRADIADSENSE_CTA_LABEL|escape:'html'}
    </a>
</section>
{/if}