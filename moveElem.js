
var moveElem_items = [
	{
		max:  800,
		act:  'append',
		from: '.topmenubox',
		to:   '.mobilemenu .col_1',
		elem: '.topmenu'
	},
	{
		min:  800,
		act:  'append',
		from: '.mobilemenu .col_1',
		to:   '.topmenubox',
		elem: '.topmenu'
	},

	{
		max:  800,
		act:  'append',
		from: '.mainmenubox',
		to:   '.mobilemenu .col_2',
		elem: '.mainmenu'
	},
	{
		min:  800,
		act:  'append',
		from: '.mobilemenu .col_2',
		to:   '.mainmenubox',
		elem: '.mainmenu'
	},

	{
		max:  800,
		act:  'prepend',
		from: '.header_contacts',
		to:   '.mainmenu_mobile',
		elem: '.callbackorder'
	},
	{
		min:  800,
		act:  'append',
		from: '.mainmenu_mobile',
		to:   '.header_contacts',
		elem: '.callbackorder'
	},

	{
		max:  800,
		act:  'prepend',
		from: '.features_button_box',
		to:   '.mainmenu_mobile',
		elem: '.features_button'
	},
	{
		min:  800,
		act:  'append',
		from: '.mainmenu_mobile',
		to:   '.features_button_box',
		elem: '.features_button'
	},
];

(function($){
	$(window).resize(function(){
		moveElem(moveElem_items);
	});

	$(document).ready(function(){
		moveElem(moveElem_items);
	});
});

function moveElem(items)
{
	/**
	 * moveElem
	 * @version 2.0
	 * 01.10.2018
	 */
	var ww = window.innerWidth;
	items.forEach(function (itm, index) {
		if (
			(
				(
					(itm['max'] && ww <= itm['max'])
					|| (itm['min'] && itm['min'] < ww)
				) && itm['if'] !== false
			) || (
				itm['onlyif']
				&& itm['onlyif']() !== false
			)
		) {
			var foreach = itm['each'] ? itm['each'] : 'body';
			$(foreach).each(function(){
				var fromelem = itm['from']==='this' ? $(itm['elem'], this) : $(itm['from']+' '+itm['elem'], this);
				if (itm['act'] == 'after' || itm['act'] == 'before') {
					var to = itm['to']==='this' ? $(this) : $(itm['to']+' '+itm['elem2'], this);
				} else {
					var to = itm['to']==='this' ? $(this) : $(itm['to'], this);
				}

				if ( ! fromelem.length && to.length) return;

				if (itm['act'] == 'append') {
					to.append(fromelem);
				} else if (itm['act'] == 'prepend') {
					to.prepend(fromelem);
				} else if (itm['act'] == 'after') {
					to.after(fromelem);
				} else if (itm['act'] == 'before') {
					to.before(fromelem);
				}

				if (itm['done']) itm['done']();
			});
		}
	});
}
