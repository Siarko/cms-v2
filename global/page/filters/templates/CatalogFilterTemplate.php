<?php $i = 1; foreach ($this->pages as $page): ?>
<?php
    $headerHtml = $page['localization']['header_text'];
    $header = Hub::get()->Util->getTextFromHtml($headerHtml);
    $contentHtml = $page['localization']['content_text'];
    $content = substr(Hub::get()->Util->getTextFromHtml($contentHtml), 0, 200).'...';

    ?>
<a href="<?=$page['id']?>">
    <div class="CF_page_position">
        <div class="CF_image" style='background-image: url("<?=$page['localization']['main_image']?>")'>
        </div>
        <div class="CF_texts">
            <div class="CF_title">
                <?=$header?>
            </div>
            <div class="CF_description">
                <?=$content?>
            </div>
        </div>
    </div>
</a>
<?php endforeach;?>
