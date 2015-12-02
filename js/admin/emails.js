Ext.onReady(function()
{
	var grid = new IntelliGrid(
	{
		columns: [
			'expander',
			{name: 'subj', title: _t('title'), width: 2, editor: 'text'},
			{name: 'date_added', title: _t('date'), width: 175, editor: 'date'},
			{name: 'from_name', title: _t('sender'), width: 150, editor: 'text'},
			{name: 'total', title: _t('amount'), width: 50, editor: 'text'},
			'delete',
			'selection'
		],
		expanderTemplate: '{body}',
		fields: ['body']
	}, false);

	grid.toolbar = Ext.create('Ext.Toolbar', {items:[
	{
		emptyText: _t('text'),
		name: 'text',
		listeners: intelli.gridHelper.listener.specialKey,
		width: 275,
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