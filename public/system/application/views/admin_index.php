<div id="container">    
    <div id="header">
        <h1>Quality Control Information System</h1>
        <div id="login" style="float:right; padding-right: 5px;"><h4><?php echo anchor('http://localhost/ci2/index.php/emp/logout/', 'Logout', 'title="Logout"'); ?></h4></div>
    </div><!--// end #header //-->
    <div id="navBar">
        <div id="rightNavBar"><form action="" method="post">
                <input name="searchword" maxlength="20"  class="inputbox" type="text" size="20" value="Search..." onblur="if (this.value=='')this.value='Search...';" onfocus="if (this.value=='Search...') this.value='';"/>
                <input type="hidden" name="task" value="search"/>
                <input type="hidden" name="option" value="com_search" />
                <input type="hidden" name="Itemid" value="243" />     </form></div>
        <!--// main navigation bar on the left //-->
        <div id="leftNavBar">
            <?php echo($content['leftNavbar']) ?>
        </div><!--// end #leftNavBar //-->
    </div><!--// end #navBar //-->    
    <div id="leftColumn">
        <div id="topLC">
            <?php echo $content['topLC'] ?>
        </div>
        <div id="bottomLC">
            <?php ?>
        </div>

    </div><!--// end #leftColumn //-->
    <div id="centerColumn">
        <div id="headCC"><h1>
                <?php echo $content['headCC'] ?></h1>
        </div>
        <hr>
        <div id="bottomCC1">
            <?php $this->load->view('/admin/' . $content['bottomCC1'] . ".php") ?>
            </div>
            <div id="bottomCC2" style="height: 100px;">
                <h2></h2>

            </div><!--// end #centerColumn //-->
        </div>

        <div id="rightColumn">
            <div id="topRC">
            <?php $this->load->view($content['topRC'] . ".php") ?>
            </div>
            <div id="bottomRC">            
        </div>
    </div><!--// end #rightColumn //-->
</div>