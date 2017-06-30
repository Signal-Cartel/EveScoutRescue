<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'discord_output.php';
use Discord;
if ($_POST) {
    $process= new Discord;
    $process->sendMessage($_POST['audience'], $_POST['user'], $_POST['alert'], $_POST['message']);
}
?>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="bootstrap.min.css"/>
</head>
<body>
<div class="dashboard">
    <div class="followMeBar title-tab text-center">
        <h2> Discord Message Send
        </h2>


    </div>
</div>
<div id="theme-wrapper">

    <div id="page-wrapper" class="container">
        <div class="row">

            <div id="content-wrapper">


                <div class="clearfix clearPad">


                    <div class="row">
                        <div class="col-lg-12">
                            <div id="" class="clearfix">
                                <div class="container">
                                    <div class="dashboard">
                                        <div>


                                            <div>
                                                <div class="row ">


                                                    <div
                                                            class="col-sm-12 sortable-list ui-sortable">
                                                        <div
                                                                style="padding-left:10px;padding-right:10px;">
                                                            <form
                                                                    action="discord_form.php" method="post"

                                                                    id="medicationForm">
                                                                <div class="col-md-4 col-sm-12 f-input  no-padding">
                                                                    <input type="hidden" name="id" value="" id="med_id">
                                                                    <div class="form-group item-font">
                                                                        <h5>User Name: </h5>
                                                                        <input type="text" name="user"
                                                                               placeholder="User Name"

                                                                               class="form-control "
                                                                        >
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-4 col-sm-12 no-padding">
                                                                    <div class="form-group item-font">
                                                                        <h5>Audience: </h5>
                                                                        <select name="audience"


                                                                                class="form-control "
                                                                                required>
                                                                            <?php foreach ($webHookArray as $key => $value) {

                                                                                echo "<option value='" . $value . "'>" . $key . "</option>";

                                                                            } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <input type="hidden" name="alert" value="0">
                                                                <div class="col-md-2 col-sm-12 f-input form-group no-padding clearfix">
                                                                    <h5>&nbsp;</h5>
                                                                    <div class="checkbox-nice"
                                                                         style="width:200px;margin:0 auto;">
                                                                        <input type="checkbox" id="alert" name="alert"
                                                                               value="1"/>
                                                                        <label for="alert">

                                                                            Alert Channel

                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 col-sm-12 no-padding">
                                                                    <div class="f-input form-group item-font">
                                                                        <h5>Message: </h5>
                                                                        <textarea name="message"
                                                                                  placeholder="Message"

                                                                                  class="form-control "
                                                                        ></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                                <div class="actions">


                                                                    <button type="submit" name="button"
                                                                            data-style="slide-up"
                                                                            class="btn btn-lg btn-success  form-control"
                                                                            style="padding-top:4px;"><span
                                                                        >Save</span></button>

                                                                </div>
                                                                <div class="clearfix"></div>
                                                            </form>
                                                            <br><br>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>


                </div>
            </div>


        </div>
    </div>
</div>


<script src="assets/js/jquery.js"></script>
<script src="assets/js/bootstrap.js"></script>
</body>
</html>



