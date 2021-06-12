<?php

$page = $datas['pageNow'];
$allPage = $datas['countPage'];

if($allPage - $page <= 6){
    $start = $allPage - 9;
    if($start <= 0){
    	$start = 1;
    } 
    $end = $allPage;
}
else if($page <= 6){
    $start = 1;
    $end = 9;
}
else{
    $start = $page - 4;
    $end = $page + 4;
}

?>

<nav aria-label="Page navigation">
    <ul class="pagination">
        <li class="page-item <?=$datas['pageNow'] == 1 ? 'disabled' : 'setGotoPage'?>">
            <a class="page-link" href="#" aria-label="Previous" data-p="<?=$datas['pageNow'] - 1?>">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Previous</span>
            </a>
        </li>
        <?php if($start > 2) : ?>
            <li class="page-item setGotoPage" data-p="1">
                <a class="page-link" href="#">1</a>
            </li>
            <li class="page-item setGotoPage" data-p="2">
                <a class="page-link" href="#">2</a>
            </li>
            <li class="page-item disabled">
                <a class="page-link" href="#">...</a>
            </li>
        <?php endif; ?>
        <?php for($i = $start; $i <= $end; $i++) : ?>
            <li class="page-item <?=$datas['pageNow'] == $i ? 'disabled' : 'setGotoPage'?>" data-p="<?=$i?>">
                <a class="page-link" href="#"><?=$i?></a>
            </li>
        <?php endfor; ?>
        <?php if($end <= ($allPage - 3)) : ?>
            <li class="page-item disabled">
                <a class="page-link" href="#">...</a>
            </li>
            <li class="page-item setGotoPage" data-p="<?=($allPage-1)?>">
                <a class="page-link" href="#"><?=$allPage-1?></a>
            </li>
            <li class="page-item setGotoPage" data-p="<?=$allPage?>">
                <a class="page-link" href="#"><?=$allPage?></a>
            </li>
        <?php endif; ?>
        <li class="page-item <?=$datas['pageNow'] == $allPage ? 'disabled' : 'setGotoPage'?>">
            <a class="page-link" href="#" aria-label="Next" data-p="<?=$datas['pageNow'] + 1?>">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Next</span>
            </a>
        </li>
    </ul>
</nav>