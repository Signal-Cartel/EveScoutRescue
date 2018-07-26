<!-- System Notes Modal Form -->
<div id="ModalSysNotes" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header sysnotes">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title sechead"><?=$system?> - System Notes</h4>
        </div>
        <div class="modal-body black">
            <?php
            foreach ($arrSysnotes as $val) {
                echo '<div style="padding-left: 2em; text-indent: -2em;">';
                echo '['. Output::getEveDatetime($val['notedate']) .' // '. $val['noteby'] .']<br />'. $val['note'] .'<br />';
                echo '</div><br />';
            }
            ?>
        </div>  
        <div class="modal-footer">
            <button type="button" class="close" data-dismiss="modal">Close</button>
        </div>
    </div>

  </div>
</div>