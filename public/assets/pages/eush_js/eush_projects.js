$(document).ready(function() {

        $("#errorsTool").hide();

        console.log("ddd");
    
        //get info on the currently logged user
    	var urlJSON = "applib/eush_eurobioimaging.php";
    	$.ajax({
                type: 'POST',
                url: urlJSON,
                data: {'action': 'getUser'}
        }).done(function(data) {
        var currentUser = data;

        //GENERAL DATATABLE
        $('#workflowsTable').DataTable( {
            "ajax": {
                url: 'applib/eush_eurobioimaging.php?action=getProjects',
                dataSrc: function (jsonData) {
                    // Here we parse JSON objects.
                    parsed = JSON.parse(jsonData)
                    var array = null
                    var table = []
                    Object.keys(parsed).forEach(function(key) {
                        array = parsed[key];
                    })
                    array = array["Result"]
                    console.log(array)
                    for (var i = 0; i < array.length; i++) {
                        // Project ID
                        id = array[i]["id"]
                        // Project Description
                        description = array[i]["description"]
                        // Project Name
                        name = array[i]["name"]
                        // Project PI
                        pi = array[i]["pi"]
                        if(array[i]["project_access"] !== "public") {
                            continue
                        }
                        // Project Access
                        access = array[i]["project_access"]
                    
                        obj = { "id" : id, 
                                "description" : description, 
                                "name" : name, 
                                "pi" : pi,
                                "access" : access
                        }        
                        table.push(obj)
                    }
                    return table;
                }
            },
            autoWidth: false,
            "columns" : [
                { "data" : "id"},
                { "data" : "description" },
                { "data" : "name" },
                { "data" : "pi" },
                { "data" : "access"}
            ],
            "columnDefs": [
                //targets are the number of corresponding columns
                { "title": "ID", "targets": 0 },
                { "title": "Description", "targets": 1 },
                { "title": "Project Name", "targets": 2 },
                { "title": "Principal Inv.", "targets": 3 },
                { "title": "Access", "targets": 4 },
                { render: function(data, type, row) {
                    // Here we should put an href to the specific project.
                    return '<a href="/vre/getdata/eush_bioimages/eush_subjects.php?project_id='+row.id+'"> '+row.id+' </a>'
                }, "targets": 0}
            ]
        });
    });

        $("#workflowsReload").click(function() {
                reload();
    });
});


