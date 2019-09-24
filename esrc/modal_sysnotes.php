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
                // list create date if no edits; otherwise, list LastUpdated date
                $htmlNoteDate = (is_null($val['LastUpdated'])) ? $val['notedate'] : $val['LastUpdated'];
                echo '['. Output::getEveDatetime($htmlNoteDate) .' // '. $val['noteby'] .']';
                // provide a visual indicator that the note has been edited
                if (!is_null($val['LastUpdated'])) { echo ' <strong>*</strong>'; }
                // the person who created the note can edit; coordinators can edit AND delete
                if ($isCoord || $charname == $val['noteby']) { 
                    echo ' <a href="' . $phpPage . '?sys=' . $system . '&noteid=' . $val['id'] . '">edit</a>'; 
                }
                if ($isCoord) { 
                    echo ' | <a href="' . $phpPage . '?sys=' . $system . '&noteid=' . $val['id'] . '&notedel=1">delete</a>';
                }
                echo '<br />'. Output::htmlEncodeString($val['note']) .'<br />';
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