<html>
    <head>
        <title>
               <?=$title?>
        </title>
    </head>
    <body>
        <h1><?=$heading?></h1>
         <?php $i=0; ?>
            <?php foreach($query->result() as $row): ?>       
        <h3><?=$row->title?></h3>
        <p><?=$row->body?></p>        
        <?=anchor('/blog/comments/'.$row->id,'Comments ('.$counts[$i]->num_rows().')');?>
        <?php $i++; ?>
        <hr>
            <?php  endforeach; ?>
        
    </body>
</html>
