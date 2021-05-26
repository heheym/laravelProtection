<html>



<script src="<?php echo e(URL::asset('js/jquery.ztree.core.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('js/jquery.ztree.excheck.min.js')); ?>"></script>
<link href="<?php echo e(URL::asset('css/zTreeStyle.css')); ?>" rel="stylesheet">

<body>
<div id="ztree" class="ztree" style="font-size:30px;overflow:auto;height:300px"></div>
</body>


<script type="text/javascript">
    $(document).ready(function() {
        loadTree();
        che();
    });

    function loadTree(){
        var data = [];

        var zNodes = [
            {name:"test1", open:true, children:[
                {name:"test1_1"}, {name:"test1_2"}]},
            {name:"test2", open:true, children:[
                {name:"test2_1"}, {name:"test2_2"}]}
        ];

        data = JSON.parse('<?php echo $data; ?>');

        loadTreeData(data);
    }

    function loadTreeData(data) {
        var setting = {
            check: {
                enable: true,
                chkStyle: "checkbox",
                chkboxType: { "Y": "ps", "N": "ps" }//勾选操作，只影响父级节点；取消勾选操作，只影响子级节点
            },
            view: {
                dblClickExpand: true,
            },
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id", //id标识  可以修改为数据库对应字段
                    pIdKey: "parent_id", //父级id
                    rootPId: 1, //根节点id
                }
            },
            callback: {
                 onCheck: onTreeClick,
            }
        };
        $.fn.zTree.init($("#ztree"), setting, data);
        var zTree = $.fn.zTree.getZTreeObj("ztree");　　// 业务，默认不展开节点　　// 实现，先展开节点，在关闭，否则取不到子节点信息

        zTree.expandAll(false);
        var rootnode = zTree.getNodeByParam("id", 1, null);
        zTree.expandNode(rootnode,true,false,true);
    }


    function onTreeClick(event, treeId, treeNode, clickFlag){
        var zTree = $.fn.zTree.getZTreeObj("ztree");
        var nodes = zTree.getCheckedNodes(true);
        var rootnode = zTree.getNodeByParam("id", 1, null);
        if(rootnode.getCheckStatus().checked && !rootnode.getCheckStatus().half){
            $('form .box-body [name=address]').remove();
            $('form .box-body').append("<input type='hidden' name='address' value='1' >");
        }else{
            var c=[];
            for(var i=0;i<nodes.length;i++) {
                if (nodes[i].isParent != true) {
                    c.push(nodes[i].id);
                }
            }
            $('form .box-body [name=address]').remove();
            $('form .box-body').append("<input type='hidden' name='address' value="+c+" >");
        }
    }

    //回显
    function che() {
//        $.fn.zTree.init($("#tree"), setting, zNodes);
        var che = '<?php echo $che; ?>';

        if(che.length>0){
            che = JSON.parse('<?php echo $che; ?>');
            $('form .box-body [name=address]').remove();
            $('form .box-body').append("<input type='hidden' name='address' value="+che+" >");
        }
        var zTreeObj = $.fn.zTree.getZTreeObj("ztree");
        var zTree = zTreeObj.getCheckedNodes(false);
        var pid= che; /**此处数据前后必须拼接;*/
        for (var i = 0; i < zTree.length; i++) {
            if(che==1){
                zTreeObj.checkNode(zTree[i], true);
            }else{
                if (pid.indexOf(zTree[i].id) != -1) {
                    zTreeObj.checkNode(zTree[i], true);
                    zTreeObj.updateNode(zTree[i],true );
                }
            }

        }

    }


</script>



</html>