<!DOCTYPE html>
<html>
<head>
    <title>WeBI 颜色拾取器</title>
	<link href="/libs/layui-v2.5.6/css/layui.css" rel="stylesheet">
	<link href="/css/webi/webi.clolor.css" rel="stylesheet">
    <style type="text/css">
		.hide{
			display: none;
		}
		.input{
			width: 400px;height:30px;
		}

    </style>
</head>
<body>
	<div style="margin: 30px auto;width: 100%;text-align:center;">
		<input type="text" id="colorPicker_1" class="WeBIColor input" onchange="show">
	</div>
	<div style="margin: 30px auto;width: 100%;text-align:center;">
		<input type="text" id="colorPicker_2" class="WeBIColor input" onchange="show_two">
	</div>
	<div role="document" class="ant-modal hide" style="margin: 0px; left: 819px; top: 91px; width: 290px; transform-origin: 330px 408px 0px;">
		<div class="ant-modal-content">
			<button aria-label="Close" class="ant-modal-close"><span class="ant-modal-close-x layui-icon">&#x1006;</span></button>
			<div class="ant-modal-header">
				<div class="ant-modal-title" id="rcDialogTitle1">
						<span>颜色拾取<i class="layui-icon" style="cursor: pointer; color: rgb(16, 142, 233); margin-left: 5px;">&#xe640;</i></span>
				</div>
			</div>
			<div class="ant-modal-body">
				<div>
					<div style="display: block; padding-top: 10px;">
						<div id="color_picker" class="circle-picker " style="width: 100%; display: flex; flex-wrap: wrap; margin-right: -14px; margin-bottom: -14px;">
							<span>
								<div style="width: 28px; height: 28px; margin-right: 14px; margin-bottom: 14px; transform: scale(1); transition: transform 100ms ease 0s;">
									<span>
									<div class="selected" title="#f44336" tabindex="0" style="background: transparent none repeat scroll 0% 0%; height: 100%; width: 100%; cursor: pointer; position: relative; outline: medium none currentcolor; border-radius: 50%; box-shadow: rgb(244, 67, 54) 0px 0px 0px 14px inset; transition: box-shadow 100ms ease 0s;">
									</div>
									</span>
								</div>
							</span>
							<span>
								<div style="width: 28px; height: 28px; margin-right: 14px; margin-bottom: 14px; transform: scale(1); transition: transform 100ms ease 0s;">
									<span>
									<div title="#e91e63" tabindex="0" style="background: transparent none repeat scroll 0% 0%; height: 100%; width: 100%; cursor: pointer; position: relative; outline: medium none currentcolor; border-radius: 50%; box-shadow: rgb(233, 30, 99) 0px 0px 0px 14px inset; transition: box-shadow 100ms ease 0s;">
									</div>
									</span>
								</div>
								</span><span>
								<div style="width: 28px; height: 28px; margin-right: 14px; margin-bottom: 14px; transform: scale(1); transition: transform 100ms ease 0s;">
									<span>
									<div title="#9c27b0" tabindex="0" style="background: transparent none repeat scroll 0% 0%; height: 100%; width: 100%; cursor: pointer; position: relative; outline: medium none currentcolor; border-radius: 50%; box-shadow: rgb(156, 39, 176) 0px 0px 0px 14px inset; transition: box-shadow 100ms ease 0s;">
									</div>
									</span>
								</div>
								</span><span>
								<div style="width: 28px; height: 28px; margin-right: 14px; margin-bottom: 14px; transform: scale(1); transition: transform 100ms ease 0s;">
									<span>
									<div title="#673ab7" tabindex="0" style="background: transparent none repeat scroll 0% 0%; height: 100%; width: 100%; cursor: pointer; position: relative; outline: medium none currentcolor; border-radius: 50%; box-shadow: rgb(103, 58, 183) 0px 0px 0px 14px inset; transition: box-shadow 100ms ease 0s;">
									</div>
									</span>
								</div>
								</span><span>
								<div style="width: 28px; height: 28px; margin-right: 14px; margin-bottom: 14px; transform: scale(1); transition: transform 100ms ease 0s;">
									<span>
									<div title="#3f51b5" tabindex="0" style="background: transparent none repeat scroll 0% 0%; height: 100%; width: 100%; cursor: pointer; position: relative; outline: medium none currentcolor; border-radius: 50%; box-shadow: rgb(63, 81, 181) 0px 0px 0px 14px inset; transition: box-shadow 100ms ease 0s;">
									</div>
									</span>
								</div>
								</span><span>
								<div style="width: 28px; height: 28px; margin-right: 14px; margin-bottom: 14px; transform: scale(1); transition: transform 100ms ease 0s;">
									<span>
									<div title="#2196f3" tabindex="0" style="background: transparent none repeat scroll 0% 0%; height: 100%; width: 100%; cursor: pointer; position: relative; outline: medium none currentcolor; border-radius: 50%; box-shadow: rgb(33, 150, 243) 0px 0px 0px 14px inset; transition: box-shadow 100ms ease 0s;">
									</div>
									</span>
								</div>
								</span><span>
								<div style="width: 28px; height: 28px; margin-right: 14px; margin-bottom: 14px; transform: scale(1); transition: transform 100ms ease 0s;">
									<span>
									<div title="#03a9f4" tabindex="0" style="background: transparent none repeat scroll 0% 0%; height: 100%; width: 100%; cursor: pointer; position: relative; outline: medium none currentcolor; border-radius: 50%; box-shadow: rgb(3, 169, 244) 0px 0px 0px 14px inset; transition: box-shadow 100ms ease 0s;">
									</div>
									</span>
								</div>
								</span><span>
								<div style="width: 28px; height: 28px; margin-right: 14px; margin-bottom: 14px; transform: scale(1); transition: transform 100ms ease 0s;">
									<span>
									<div title="#00bcd4" tabindex="0" style="background: transparent none repeat scroll 0% 0%; height: 100%; width: 100%; cursor: pointer; position: relative; outline: medium none currentcolor; border-radius: 50%; box-shadow: rgb(0, 188, 212) 0px 0px 0px 14px inset; transition: box-shadow 100ms ease 0s;">
									</div>
									</span>
								</div>
								</span><span>
								<div style="width: 28px; height: 28px; margin-right: 14px; margin-bottom: 14px; transform: scale(1); transition: transform 100ms ease 0s;">
									<span>
									<div title="#009688" tabindex="0" style="background: transparent none repeat scroll 0% 0%; height: 100%; width: 100%; cursor: pointer; position: relative; outline: medium none currentcolor; border-radius: 50%; box-shadow: rgb(0, 150, 136) 0px 0px 0px 14px inset; transition: box-shadow 100ms ease 0s;">
									</div>
									</span>
								</div>
								</span><span>
								<div style="width: 28px; height: 28px; margin-right: 14px; margin-bottom: 14px; transform: scale(1); transition: transform 100ms ease 0s;">
									<span>
									<div title="#4caf50" tabindex="0" style="background: transparent none repeat scroll 0% 0%; height: 100%; width: 100%; cursor: pointer; position: relative; outline: medium none currentcolor; border-radius: 50%; box-shadow: rgb(76, 175, 80) 0px 0px 0px 14px inset; transition: box-shadow 100ms ease 0s;">
									</div>
									</span>
								</div>
								</span><span>
								<div style="width: 28px; height: 28px; margin-right: 14px; margin-bottom: 14px; transform: scale(1); transition: transform 100ms ease 0s;">
									<span>
									<div title="#8bc34a" tabindex="0" style="background: transparent none repeat scroll 0% 0%; height: 100%; width: 100%; cursor: pointer; position: relative; outline: medium none currentcolor; border-radius: 50%; box-shadow: rgb(139, 195, 74) 0px 0px 0px 14px inset; transition: box-shadow 100ms ease 0s;">
									</div>
									</span>
								</div>
								</span><span>
								<div style="width: 28px; height: 28px; margin-right: 14px; margin-bottom: 14px; transform: scale(1); transition: transform 100ms ease 0s;">
									<span>
									<div title="#cddc39" tabindex="0" style="background: transparent none repeat scroll 0% 0%; height: 100%; width: 100%; cursor: pointer; position: relative; outline: medium none currentcolor; border-radius: 50%; box-shadow: rgb(205, 220, 57) 0px 0px 0px 14px inset; transition: box-shadow 100ms ease 0s;">
									</div>
									</span>
								</div>
								</span><span>
								<div style="width: 28px; height: 28px; margin-right: 14px; margin-bottom: 14px; transform: scale(1); transition: transform 100ms ease 0s;">
									<span>
									<div title="#ffeb3b" tabindex="0" style="background: transparent none repeat scroll 0% 0%; height: 100%; width: 100%; cursor: pointer; position: relative; outline: medium none currentcolor; border-radius: 50%; box-shadow: rgb(255, 235, 59) 0px 0px 0px 14px inset; transition: box-shadow 100ms ease 0s;">
									</div>
									</span>
								</div>
								</span><span>
								<div style="width: 28px; height: 28px; margin-right: 14px; margin-bottom: 14px; transform: scale(1); transition: transform 100ms ease 0s;">
									<span>
									<div title="#ffc107" tabindex="0" style="background: transparent none repeat scroll 0% 0%; height: 100%; width: 100%; cursor: pointer; position: relative; outline: medium none currentcolor; border-radius: 50%; box-shadow: rgb(255, 193, 7) 0px 0px 0px 14px inset; transition: box-shadow 100ms ease 0s;">
									</div>
									</span>
								</div>
								</span><span>
								<div style="width: 28px; height: 28px; margin-right: 14px; margin-bottom: 14px; transform: scale(1); transition: transform 100ms ease 0s;">
									<span>
									<div title="#ff9800" tabindex="0" style="background: transparent none repeat scroll 0% 0%; height: 100%; width: 100%; cursor: pointer; position: relative; outline: medium none currentcolor; border-radius: 50%; box-shadow: rgb(255, 152, 0) 0px 0px 0px 14px inset; transition: box-shadow 100ms ease 0s;">
									</div>
									</span>
								</div>
								</span><span>
								<div style="width: 28px; height: 28px; margin-right: 14px; margin-bottom: 14px; transform: scale(1); transition: transform 100ms ease 0s;">
									<span>
									<div title="#ff5722" tabindex="0" style="background: transparent none repeat scroll 0% 0%; height: 100%; width: 100%; cursor: pointer; position: relative; outline: medium none currentcolor; border-radius: 50%; box-shadow: rgb(255, 87, 34) 0px 0px 0px 14px inset; transition: box-shadow 100ms ease 0s;">
									</div>
									</span>
								</div>
								</span><span>
								<div style="width: 28px; height: 28px; margin-right: 14px; margin-bottom: 14px; transform: scale(1); transition: transform 100ms ease 0s;">
									<span>
									<div title="#795548" tabindex="0" style="background: transparent none repeat scroll 0% 0%; height: 100%; width: 100%; cursor: pointer; position: relative; outline: medium none currentcolor; border-radius: 50%; box-shadow: rgb(121, 85, 72) 0px 0px 0px 14px inset; transition: box-shadow 100ms ease 0s;">
									</div>
									</span>
								</div>
								</span><span>
								<div style="width: 28px; height: 28px; margin-right: 14px; margin-bottom: 14px; transform: scale(1); transition: transform 100ms ease 0s;">
									<span>
									<div title="#607d8b" tabindex="0" style="background: transparent none repeat scroll 0% 0%; height: 100%; width: 100%; cursor: pointer; position: relative; outline: medium none currentcolor; border-radius: 50%; box-shadow: rgb(96, 125, 139) 0px 0px 0px 14px inset; transition: box-shadow 100ms ease 0s;">
									</div>
									</span>
								</div>
								</span>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</body>

<script src="/js/webi/webi.color.js"></script>

<script>

	function show(){
	    console.log('come here first');
	}

    function show_two(){
        console.log('come here two');
    }

//	var obj = document.getElementById('color_picker');
//    var list = obj.querySelectorAll('.selected');
//    for ( k in list ){
//        console.log(list[k].title);
//	}

</script>

</html>