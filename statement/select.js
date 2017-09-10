<script type = "text/javascript">

function functionName()
{
var select1 = document.forms.formName.selectName1; 
var select2 = document.forms.formName.selectName2; 

select2.options.length = 0; // 選択肢の数がそれぞれに異なる場合、これが重要

if (select1.options[select1.selectedIndex].value == "果物")
{
select2.options[0] = new Option("りんご");
select2.options[1] = new Option("みかん");
select2.options[2] = new Option("オレンジ");
}

else if (select1.options[select1.selectedIndex].value == "野菜")
{
select2.options[0] = new Option("キャベツ");
select2.options[1] = new Option("きゅうり");
select2.options[2] = new Option("にんんじん");
select2.options[3] = new Option("たまねぎ");
}

else if (select1.options[select1.selectedIndex].value == "肉類")
{
select2.options[0] = new Option("豚肉");
select2.options[1] = new Option("牛肉");
}
}

</script>
