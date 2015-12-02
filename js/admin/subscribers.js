Ext.onReady(function()
{
	var grid = new IntelliGrid(
	{
		columns: [
			'selection',
			{name: 'fullname', title: _t('fullname'), width: 1, editor: 'text'},
			{name: 'email', title: _t('email'), width: 1, editor: 'text'},
			{name: 'date', title: _t('date'), width: 170},
			'status',
			'delete'
		],
		sorters: [{property: 'date', direction: 'DESC'}]
	}, false);

	grid.toolbar = Ext.create('Ext.Toolbar', {items:[
	{
		emptyText: _t('fullname'),
		name: 'fullname',
		listeners: intelli.gridHelper.listener.specialKey,
		width: 150,
		xtype: 'textfield'
	},{
		emptyText: _t('email'),
		name: 'email',
		listeners: intelli.gridHelper.listener.specialKey,
		width: 150,
		xtype: 'textfield'
	},{
		displayField: 'title',
		editable: false,
		emptyText: _t('status'),
		id: 'fltStatus',
		name: 'status',
		store: grid.stores.statuses,
		typeAhead: true,
		valueField: 'value',
		xtype: 'combo'
	},{
		handler: function(){intelli.gridHelper.search(grid);},
		id: 'fltBtn',
		text: '<i class="i-search"></i> ' + _t('search')
	},{
		handler: function(){intelli.gridHelper.search(grid, true);},
		text: '<i class="i-close"></i> ' + _t('reset')
	}]});

	grid.init();

	var searchStatus = intelli.urlVal('status');
	if (searchStatus)
	{
		Ext.getCmp('fltStatus').setValue(searchStatus);
		intelli.gridHelper.search(grid);
	}
});
