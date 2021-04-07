
# Virtual Research Environment (VRE) administration
 VRE is a Virtual Research Environment including a web application and a cloud-based backend aimed to provide end-users an integrative platform for exploring rellevant data resources and applying to them a collection of analyses and visualizations. It is written in PHP, HTML and Javascript
 This document describes how to install ans configure VRE platform in your system, as well as how to process on several basic operation for customizing VRE to your specific project needs.
 
 - [Installation](#installation)
 - [Install a Tool](#install_my_first_tool)
        - [Install wget](#install_wget_tool)
 - [Create a New Page](#create_new_page)
 - [Create a New Dataset](#create_new_dataset)

<a name="installation"></a>
----
# Installation
----

### Get VRE code

Clone VRE code in your installation directory, for instance `/home/user`

```
git clone https://github.com/inab/openvre.git
cd openvre
```

### Install library dependencies
Install PHP dependencies via composer using the following command
```
composer update
```

Make user PHP is able to use some extension, like CURL and GD. Install the corresponding packages and enable them at the `php.ini` uncommenting the following directives:

```
extension=curl
extension=gd2

```
After installing the packages, restart the web server:

```
sudo apt-get install libssh2-1-dev
sudo apt-get install php-curl php-gd php-ssh2 php-mbstring
sudo service apache2 restart

```


### Configure web access

The `public/` directory is to be accessible on the web, and the server should accept '.htaccess' files. So, the Apache2 web server is to be accordingly set. 
Multiple configurations are possible, for instance, you can set the host files mapping using the [Alias](https://httpd.apache.org/docs/2.4/mod/mod_alias.html) directive, or you can prepare a new virtual host by which the root document points to the openVRE public folder:

```
DocumentRoot /home/user/openvre/public/

<Directory "/home/user/openvre/public">
        Options Indexes FollowSymLinks
        AllowOverride All 
        Require all granted
</Directory>
```

Enabling the rewriting engine is also required:

```
sudo a2enmod rewrite
systemctl restart apache2
```


### Prepare directory structure

Define where VRE user's data and other data is to be stored, for instance, in `/data` . Copy the directory structure there, and give apache/nginx user permissions to it:

```
cp -r install/data/* /data/
chown -R  www-data:www-data /data/
```

For some visualizers, direct access to this data is required. So, if you latter plan to install one of these visualizers (e.g. NGL), add theses soft links:

```
cd public
ln -s /data/userdata/ files  
ln -s /data/public/refGenomes/ refGenomes  # obsolete
ln -s /data/tool_schemas/son* json* # obsolete
```

### Create and populate mongo DB

Create a new database to Mongo, here `dbname`, and populate it with the structural collections:

```
for f in install/database/*.json; do mongoimport --db dbname -u myAdmin -p XXXX --authenticationDatabase admin $f; done
```

### Configure application settings

Most of the VRE settings are parameterized at  `config/globals.inc.php`. Copy the template and read through it to configure arguments like the data path, the URL, the database, etc.

```
cp config/globals.inc.php.sample config/globals.inc.php
```
Each parameter is briefly introduced, yet most of them are self-explanatory. Some of these configurations require extra settings or auxiliary files. These are listed below:


##### *logFile*: Configure log file

Configure the appropiate path in `config/globals.inc.php`, and make sure it exists and is writabble by Apache's user:
```
mkdir -p /home/user/openvre/logs/
touch /home/user/openvre/logs/application.log
sudo chown -R www-data:www-data  /home/user/openvre/logs/application.log

```

##### *mail_credentials* : Configure SMTP mail account
Copy the conf template, set the credentials in there and parameterize `config/globals.inc.php` accordingly

```
cp config/mail.conf.sample config/mail.conf 
```

##### *db_credentials*: Configure Mongo DB access

Copy the conf template, set the credentials in there and parameterize `config/globals.inc.php` accordingly. VRE will use the PHP MongoDB driver to connect to the database.

```
cp config/mongo.conf.sample config/mongo.conf 
```

##### *auth_credentials*: Configure Oauth2 client
Copy the conf template, set the credentials in there and parameterize `config/globals.inc.php` accordingly, including the openID endpoints. VRE uses a generic OpenID Connect Resource Provider that connects to a external identity manager server (https://www.keycloak.org/)

```
cp config/oauth2.conf.sample config/oauth2.conf 
cp config/oauth2_admin.conf.sample config/oauth2_admin.conf 
```
##### *Add Logos and FavIcon*

Replace these files with your project logos

- `public/assets/layouts/layout/img/logo-big.png` :  logo for central frame pages 
- `public/assets/layouts/layout/img/logo.png`  :  logo for top navigation bar
- `public/assets/layouts/layout/img/icon.png` :  favico logo

##### *Custom CSS*

Edit the custom CSS file for customizing the web look

- `public/assets/layouts/layout/css/custom.min.css`


##### *Add static help pages content*

Edit the following pages, mostly HTML, for the Help section
- public/help/general.php : Section 'Help' > 'General Information'
- public/help/starting.php : Section 'Help' > 'Getting Started'
- public/help/upload.php : Section 'Help' > 'Get Data'
- public/help/ws.php : Section 'Help' > 'Workspace'
- public/help/launch.php : Section 'Help' > 'Launch Job'
- public/help/hdesk.php : Section 'Help' > 'Helpdesk'
- public/help/related.php : Section 'Help' > 'Related Links'
- public/help/refs.php : Section 'Help' > 'References'
- public/help/ackn.php : Section 'Help' > 'Acknowledgments'

##### *Add terms of use*

Edit the following page with your project's terms of use. They are displayed at the VRE footer page.

```
vim public/applib/getTermsOfUse.php
```

##### *Prepare default data of user's workspace*

Edit the content for the README file that appears by default on each workspace in `upload/README`.

```
vim /data/sampleData/basic/uploads/README.md`
```

##### *Add First Admin user*

Log into VRE and register with the user you want to convert into the admin user. Then, grant it admin privileges by accessing the Mongo database and update `Type:0` 

``` 
db.users.update(
   { _id: admin@mail.com },
   { $set: { "Type": 0 } }
)
```


### Configure SGE

Set the current system as a master SGE host able to submit jobs. Configure also a local queue, if you plan to install tools locally (i.e. WGET tool, BAMvalidation).

```
sudo qconf -as mail.domain.es
```

### Maintainance tasks

Each time a new user accesses the VRE, an anonymous account is transparently created until it registers. It generates a serie of entries in both, the data directory and the Mongo database that should be regularly cleaned. Set a cron job for *deleting these temporal users*, as well as the *expired data* - if you configured it at `config/globals.inc.php`.
You can build your own script or use `scripts/maintainance/cleanUsersData.php`. The first lines of the script contains configurable arguments, read and set them before actually running it. Make sure the cron job runs under a UNIX user able to delete VRE data, for instance, the same UNIX user serving VRE (www-data), or the root user.

```
crontab -e
```
```
# m h  dom mon dow   command
00 5  *   *   *      /path/to/VRE/scripts/maintainance/cleanUsersData.php >> /path/to/VRE/logs/cleanUsersData.log 2>&1
```

<a name="install_my_first_tool"></a>
----
# Install a Tool
----


### Register the tool into the database

Register the tool definition into the `tools collection` database

1. Prepare JSON schema for your project's tools extending the following definition:
        - `install/data/tool_schemas/tool_specification/tool_schema.json`

2. Tool developer is to prepare the tool definition and submit it to admin. Validate the `newTool.json` against the previous schema. Following, an example:
        - `install/data/tool_schemas/tool_specification/examples/example.json`

3. Insert the tool document into the tools collection as a new entry
        ```
        mongoimport --jsonArray --db dbname --collection tools -u myAdmin -p XXXX --authenticationDatabase admin --file newTool.json
        ```

### Prepare tool form

Each tool in VRE requires a new folder in `tools/`. Create it for the 'newTool'. You can take the tool_skeleton as a template

```
cp tools/tool_skeleton/ tools/newTool
sudo chown www-data:www-data tools/newTool/help/img/
```

Create the tool form where the user is to set the arguments and input files for a run (ie. Launch Tool > Tool Skeleton > Start Tool with 1 fasta). Use the tool_skeleton/input.php as a template, and edit it according the number and type of input files and arguments you need for your newTool

```
vim tools/newTool/inputs.php
```

Edit:
- `$toolId = "tool_skeleton";` :  set to your tool id (newTool)
- `$tool['input_files']['fasta1']`: set your input file name/s. Substitute in the following code the tool_skeleton 'fasta1' input by the identificator of your input file - according your tool definition. Concatenate and adapt as many <div/> as input files your tool have.
    ```html
        <div class="col-md-12">
            <?php $ff = matchFormat_File($tool['input_files']['genes']['file_type'], $inPaths); ?>
            <?php InputTool_printSelectFile($tool['input_files']['genes'], $rerunParams['fasta1'], $ff[0], false, true); ?>
        </div>
    ```

## Enable tool help pages

Register tool help pages to the database. Each section help has a Mark Down editor where the tool developer will be able to set the help content by himself.
For registering the complete list of help sections (Help > Tools > Tool Skeleton > Methods, Inputs, Outputs, Results, References), add the following documents into `helps collection`.


```json
{ 
    "tool" : "tool_skeleton", 
    "help" : "references", 
    "title" : "References", 
    "content" : "<!-- Help content -->\r\n# Title 1\r\nEdit the content using the online *Markdown* editor."
}
{ 
    "tool" : "tool_skeleton", 
    "help" : "results", 
    "title" : "Results", 
    "content" : "<!-- Help content -->\r\n# Title 1\r\nEdit the content using the online *Markdown* editor."
}
{ 
    "tool" : "tool_skeleton", 
    "help" : "method", 
    "title" : "Method", 
    "content" : "<!-- Help content -->\r\n# Title 1\r\nEdit the content using the online *Markdown* editor.\r\n"
}
{ 
    "tool" : "tool_skeleton", 
    "help" : "outputs", 
    "title" : "Outputs", 
    "content" : "<!-- Help content -->\r\n# Title 1\r\nEdit the content using the online *Markdown* editor.\r\n"
}
{ 
    "tool" : "tool_skeleton", 
    "help" : "help", 
    "title" : "Tool Skeleton", 
    "content" : "<!-- Example of navigator -->\r\n<a href=\"/tools/tool_skeleton/help/method.php\" class=\"btn green btn-xs\">Method</a> <a href=\"/tools/tool_skeleton/help/inputs.php\" class=\"btn green btn-xs\">Inputs</a> <a href=\"/tools/tool_skeleton/help/outputs.php\" class=\"btn green btn-xs\">Outputs</a> <a href=\"/tools/tool_skeleton/help/results.php\" class=\"btn green btn-xs\">Results</a> <a href=\"/tools/tool_skeleton/help/references.php\" class=\"btn green btn-xs\">References</a>\r\n\r\n<!-- Help content -->\r\n# Title 1\r\nEdit the content using the online *Markdown* editor.\r\n\r\n\r\n"
}
{ 
    "tool" : "tool_skeleton", 
    "help" : "inputs", 
    "title" : "Inputs", 
    "content" : "<!-- Help content -->\r\n# Title 1\r\nEdit the content using the online *Markdown* editor."
}
```

### Prepare tool custom viewer 

Apart from generating output files that are to be displayed in the workspace, a tool may also have a custom displayer, a simple HTML template that is going to source a tool output TAR for showing the statistics, logs, etc, associated to each run. 

1. Make sure the tool has registered an output file that have `data_type: tool_statistics` and `file_type : TAR`. Also, `has_custom_viewer : true` . 
2. Edit the basic `output.php` from tool_skeleton, and extend it by preparing the custom display for the newTool.
    ```
    vim tools/newTool/outputs.php
    ```
    ```html
        <div class="col-md-12">
            <?php $ff = matchFormat_File($tool['input_files']['genes']['file_type'], $inPaths); ?>
            <?php InputTool_printSelectFile($tool['input_files']['genes'], $rerunParams['fasta1'], $ff[0], false, true); ?>
        </div>
    ```
3. Check the result at 'View Results', the option appearing in the violet toolkit dropdown present next to each run folder at the workspace table.


<a name="create_new_page"></a>
----
## Create a new page
----

## Create content page

Use any other page as template for creating the new content of `myNewPage.php`.

> TODO: create a white page to be used as a template

## Configure headers

Add headers for including the required CSS and JS scripts. Three files need to be edited:

1. public/htmlib/menu.inc.php
2. public/htmlib/header.inc.php
3. public/htmlib/js.inc.php



<a name="install_wget_tool"></a>
----
# Install wget
----

VRE has not only tools generated and imported by tool developers. *Internal tools* are tools that the end user do not select them from the web, but are internally triggered by VRE processes. For instance:
 - File uploads from URL triggers 'wget' tool
 - Importing BAM file triggers 'BAMval' tool
In consequence, the tool executable is distributed together with the VRE code, as well the entry in the tools collection. Following, the instructions on how to configure 'wget' tool 

#### WGET
At the database, the 'Tools' collection should already have the 'wget' tool entry. Update the JSON document to adapt it to your instalation requirements:
'excutable' and 'queue':

```
"executable" : "/ABSOLUTE/PATH/TO/VRE/DATA/apps/internalTools/wget/wget.sh"
"clouds" : {
  "mug-bsc" : {
     "launcher" : "SGE", 
     "queue" : "local.q"
}

```

- 'executable' : Point it to your 'apps' folder. Should have been installed in your VRE data directory as part of the data distributed in `install/data/apps`
- 'mug-bsc' : Name of your cloud as set in `install/globals`
- 'queue': Name of the queue configured in the host where the executable is found


<a name="create_new_dataset"></a>
----
# Create new dataset
----
VRE dataset appear listed in "Get Data > Import example dataset". On select, VRE loads certain data into user's workspace the data. 


### Upload actual data

Add into the sample data directory (`GLOBALS['sampleData']` as defined in config/globals.inc.php) a new folder with the following directory tree:
- NewDataSet/
    - *.sample_metadata.json*
    - uploads/
        - myInput1.txt
    - [resultsFolder/]
        - [myOutput1.txt]
    - [...]

The main directory (here NewDataSet/) can contain *n* folders, and in turn, each folder can contain *n* files - subfolders are not supported. You can take as example the Basic sample Data:

- `install/data/sampleData/basic/` : Basic data preloaded in each new workspace. Currently contains only a simple README file.

### Create metadata

For each directory of your dataset, create a `.sample_metadata.json` file. The document sets the metadata for each of the elements (files and folder) contained in such directory. You can take those included in the Basic sample data as examples:

- NewDataSet/
    - *.sample_metadata.json*
    - uploads/
        - *.sample_metadata.json*
        - myInput1.txt
    - [resultsFolder/]
        - [*.sample_metadata.json*]
        - [myOutput1.txt]
    - [...]

- `install/sampleData/basic/uploads/.sample_metadata.json` : Metadata for 'README'  file contained  in the 'uploads/' directory

### Register the sample data

Add a new document in the `sampleData` collection defining where the new dataset is to be found

```json
{ 
    "_id" : "NewDataSet_identifier", 
    "name" : "New Dataset", 
    "sample_path" : "NewDataSet/", 
    "status" : 1, 
    "short_description" : "Short description of the contents for this new dataset"
}
```

Where "sample_path" is the relative path of the dataset from GLOBALS['sampleData'].

