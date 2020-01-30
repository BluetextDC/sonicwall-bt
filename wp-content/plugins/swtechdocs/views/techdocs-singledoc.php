<div class="td-single-doc-container">
    <h1 class="td-document-title"><?php echo getDocumentTitle(true, 'title');?></h1>
    <div class="td-breadcrumbs">
        <?php echo $breadcrumbs_content;?>
    </div>

  
    <div id="toc_container">
        <?php echo $toc_content;?>
    </div>

    <div id="book_container">
        <?php echo $book_content;?>
        
        <?php echo $next_previous_content;?>

        <?php echo $up_down_vote_content;?>
    </div>
</div>