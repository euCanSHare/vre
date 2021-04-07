<?php

require __DIR__ . "/../../config/bootstrap.php";
redirectOutside();

?>

<?php require "../htmlib/header.inc.php"; ?>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-container-bg-solid page-sidebar-fixed">
    <div class="page-wrapper">

        <?php require "../htmlib/top.inc.php"; ?>
        <?php require "../htmlib/menu.inc.php"; ?>

        <!-- BEGIN CONTENT -->
        <div class="page-content-wrapper">
            <!-- BEGIN CONTENT BODY -->
            <div class="page-content" id="body-help">
                <!-- BEGIN PAGE HEADER-->
                <!-- BEGIN PAGE BAR -->
                <div class="page-bar">
                    <ul class="page-breadcrumb">
                        <li>
                            <a href="/home/">Home</a>
                            <i class="fa fa-circle"></i>
                        </li>
                        <li>
                            <span>Help</span>
                            <i class="fa fa-circle"></i>
                        </li>
                        <li>
                            <span>General Information</span>
                        </li>
                    </ul>
                </div>
                <!-- END PAGE BAR -->
                <!-- BEGIN PAGE TITLE-->
                <h1 class="page-title">What is the euCanSHare Virtual Research Envirnoment (VRE)?</h1>
                <!-- END PAGE TITLE-->
                <!-- END PAGE HEADER-->

                <div class="note note-danger" style="background-color: #F3D8DD;border-color:#e4032e;">
		    <h4 class="block">
			The euCanSHare VRE is a computational platform designed for Data Quality Control, Cardiac Image Analysis, Machine Learning and Bioinformatics Analysis.
                    </h4>
                </div>

                <p><img src="https://eucanshare.bsc.es/dataportal/wp-content/uploads/2019/11/portal_home.png" style="margin:0 350px;width:20%;max-width:100%;" /></p>

		<p>
		The euCanSHare VRE is being developed so as to cater for the needs of the scientific community, especially clinical researchers who need an objective and quantitative way to analyse their data in an unbiased way and make predictions with state-of-the art techniques. Its main objectives are:

                <p>
                    The goals of OpenEBench are to:

                    <ul>
                        <li>The democratization of the use of Machine Learning methods by the whole community by providing a easy-to-use and high-level interface with detailed documentation.</li>
                        <li>To establish a reproducible and publicly available analysis pipeline to enhance repeatability of research results.</li>
                    </ul>
		</p>
		<p>
		   The main components of the euCanSHare VRE are:
		    <ul>
			<li>Tools for data quality control</li>
			<li>The Cardiac Image Analyzer consisting of three tools: DICOM 2 NIFTI Converter, Segmentation Tool, Radiomics Analysis Tool</li>
			<li>Machine Learning Toolbox</li>
			<li>Bioinformatics Toolbox</li>
                    </ul>

		</p>

            </div>
            <!-- END CONTENT BODY -->
        </div>
        <!-- END CONTENT -->

        <?php

        require "../htmlib/footer.inc.php";
        require "../htmlib/js.inc.php";

        ?>
