$(function(){
	$.each(intelli.config.packages, function(index, value){
		var url = intelli.config.ia_url + 'newsletters/read.json?extras=' + value.name;
		intelli.category = {parent: 0, selected: 1};
		$('#' + value.name + '-tree').jstree({
			core: {
				to_open: 0,
				initially_open: intelli.categories ? intelli.categories : []
			},
			json_data: {
			ajax: {
				url: url,
				data : function (n) {
					var result = {'p': intelli.category.parent};
					if (n.attr)
					{
						result['i'] = n.attr('id');
					}
					var currentCategory = $('#current-tree-category');
					if (currentCategory.length)
					{
						result['c'] = currentCategory.val();
					}
					return result;
					}
				}
			},
			checkbox: {
				override_ui: true,
				checked_parent_open: true
			},
			plugins: ['themes', 'json_data', 'checkbox', 'ui', 'types']
		})
		.delegate('a', 'click', function (e, data)
		{
			e.preventDefault();
		})
		.bind('click.jstree', function (e, data)
		{
			var items = $(this).jstree('get_checked');
			var item_ids = '';
			$.each(items, function(i, item){
				item_ids += item.id;
				if(i != (items.length - 1))
					item_ids += ',';
			});
			$('#' + value.name + '-catids').val(item_ids);
		})
	});
	
	$('#newsletters-save').click(function(){
		var topics = new Object();
		var url = intelli.config.ia_url + 'newsletters/edit.json';
		var error = false;
		var msg = new Object();
		
		$.each(intelli.config.packages, function(index, value){
			topics[value.name] = $('#' + value.name + '-catids').val();
		});
		
		$.ajaxSetup({async:false});
		$.post(url, {topics:topics, token:$(this).data('token')}, function(data){
			intelli.notifFloatBox({msg: data.msg, type: data.error ? 'notif' : 'error', autohide: true});
		});
		$.ajaxSetup({async:true});
	});
});