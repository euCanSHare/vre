<?php

require __DIR__ . "/../../../config/bootstrap.php";
require __DIR__ . "/config/bootstrap.php";

?>

<?php
require "../../htmlib/header.inc.php";
require "../../htmlib/js.inc.php"; ?>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-container-bg-solid page-sidebar-fixed">
    <div class="page-wrapper">
        <?php require "../../htmlib/top.inc.php"; ?>
        <?php require "../../htmlib/menu.inc.php"; ?>
        <!-- BEGIN CONTENT -->
        <div class="page-content-wrapper">
            <!-- SPINNER -->
            <div id="loading">
                <div class="cv-spinner">
                    <span class="spinner"> </span>
                </div>
            </div>
            <!-- BEGIN CONTENT BODY -->
            <div class="page-content">
                <!-- BEGIN PAGE TITLE-->
                <h1 class="page-title"> Cardiovascular GWAS:
                    <small> MAP KINASES AND TAB PROTEINS </small>
                </h1>

                <select name="phenotype" id="phenotype" class="form-control input-lg">
                    <option value="">Select phenotype</option>
                </select>
                <br />
                <select name="gene" id="gene" class="form-control input-lg">
                    <option value="">Select gene</option>
                </select>
                <br />

                <button id='add' class="btn green"> Get variants </button>
                <br/>

                <table id="gwasTable" class="table table-striped table-hover table-bordered"></table>
                <div id="gwasButton"></div>
                <div class="btn-group" style="float:right;">
                    <div class="actions">
                        <a id="tableReload" class="btn green"> Reload</a>
                    </div>
                </div>
                <!-- BEGIN ERRORS DIV -->
                <div id="errorsTool" style="display:none;"></div>
                    <div class="row">
                        <div class="col-md-12">
                        <?php
                        $error_data = false;
                        if (isset($_SESSION['errorData'])) {
                            $error_data = true;
                      
                            if (isset($_SESSION['errorData']['Info'])) { ?>
                                <div class="alert alert-info">
                             <?php } else { ?>
                                <div class="alert alert-danger">
                             <?php }
                             foreach ($_SESSION['errorData'] as $subTitle => $txts) {
                                        print "<strong>$subTitle</strong><br/>";
                                        foreach ($txts as $txt) {
                                            print "<div>$txt</div>";
                                        }
                             }
                             unset($_SESSION['errorData']);
                             ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>    
    </div>

<?php
    require "../../htmlib/footer.inc.php";
?>
