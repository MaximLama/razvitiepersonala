<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<?
$ids = [];
foreach ($arResult["ITEMS"] as $item) {
    $ids[] = $item["PROPERTIES"]["PROGRAMMA"]["VALUE"];
}
$res = CIBlockSection::GetList(
    array("ID" => $ids),
    array("IBLOCK_ID" => 26, "ID" => $ids, "ACTIVE" => "Y"),
    false,
    array("ID", "IBLOCK_ID", "SECTION_PAGE_URL", "PICTURE"),
    false
);
$sections = [];
while ($sect = $res->GetNext()) {
    $sections[] = $sect;
}
$temp = $sections;
foreach ($temp as $sect) {
    $key = array_search($sect["ID"], $ids);
    $sections[$key] = $sect;
}
?>

<? foreach ($arResult["ITEMS"] as $key => $item): ?>
    <div class="prog-study__item">
        <? if ($item["PREVIEW_PICTURE"]["SRC"]): ?>
            <div class="prog-study__img-box">
                <img src="<?= $item["PREVIEW_PICTURE"]["SRC"] ?>" alt="<?= $item["PREVIEW_PICTURE"]["ALT"] ?>">
            </div>
        <? endif; ?>
        <div class="prog-study__text-block">
            <div class="prog-study__item-title"><?= $item["NAME"] ?></div>
            <p class="prog-study__item-description">Специально разработанные программы</p>
            <a href="<?= $sections[$key]["SECTION_PAGE_URL"] ?>" class="prog-study__item-link">
                <span>Перейти</span>
                <svg viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.5037 8.34735L2.20797 14.641C1.93171 14.9165 1.48414 14.9165 1.20719 14.641C0.930937 14.3654 0.930937 13.9179 1.20719 13.6423L7.00356 7.84804L1.20789 2.05376C0.931634 1.77821 0.931634 1.33064 1.20789 1.05438C1.48414 0.778828 1.93241 0.778828 2.20866 1.05438L8.5044 7.34802C8.77647 7.62073 8.77647 8.07534 8.5037 8.34735Z"
                          stroke-width="0.5"/>
                </svg>
            </a>
            <a href="<?= $sections[$key]["SECTION_PAGE_URL"] ?>" class="prog-study__item-btn">
                <svg viewBox="0 0 26 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.3085 10.8146C4.35208 10.2717 4.59857 9.76503 4.99888 9.39564C5.3992 9.02624 5.92396 8.82118 6.46867 8.82129H19.5315C20.0762 8.82118 20.601 9.02624 21.0013 9.39564C21.4016 9.76503 21.6481 10.2717 21.6917 10.8146L22.5616 21.648C22.5855 21.9461 22.5475 22.2459 22.4498 22.5286C22.3522 22.8113 22.1971 23.0707 21.9942 23.2906C21.7914 23.5104 21.5453 23.6858 21.2714 23.8058C20.9974 23.9259 20.7016 23.9879 20.4025 23.988H5.59767C5.29858 23.9879 5.00276 23.9259 4.72881 23.8058C4.45487 23.6858 4.20874 23.5104 4.00592 23.2906C3.80311 23.0707 3.64799 22.8113 3.55035 22.5286C3.45271 22.2459 3.41466 21.9461 3.43859 21.648L4.3085 10.8146V10.8146Z"
                          stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17.3337 12.0713V6.65462C17.3337 5.50535 16.8771 4.40315 16.0645 3.59049C15.2518 2.77784 14.1496 2.32129 13.0003 2.32129C11.8511 2.32129 10.7489 2.77784 9.9362 3.59049C9.12354 4.40315 8.66699 5.50535 8.66699 6.65462V12.0713"
                          stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
    </div>
<? endforeach; ?>