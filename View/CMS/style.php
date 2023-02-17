/*Menu superior*/
.header{
	background-color: <?= $this->Main->getStyle('header-bg'); ?> !important;
}
.header .text-dark-50
{
	color: <?= $this->Main->getStyle('header-tx'); ?> !important;
}
/*Submenu superior*/
.subheader.subheader-solid {
	background-color: <?= $this->Main->getStyle('subheader-bg'); ?>;	
}

/*Fundo do logotipo*/
.brand{
	background-color: <?= $this->Main->getStyle('brand-bg'); ?>;	
}
/*Menu lateral*/
.aside-menu, .aside-menu-wrapper{
	background-color: <?= $this->Main->getStyle('aside-bg'); ?>;	
}

/*Cor do ícone do menu lateral*/
.aside-menu .menu-nav > .menu-item > .menu-heading .menu-icon i,
.aside-menu .menu-nav > .menu-item > .menu-link .menu-icon i
{
	color: <?= $this->Main->getStyle('aside-ic'); ?> ;
}

/*Cor do ícone do menu lateral quando ativo/aberto*/
.aside-menu .menu-nav > .menu-item.menu-item-open > .menu-heading .menu-icon i,
.aside-menu .menu-nav > .menu-item.menu-item-open > .menu-link .menu-icon i,
.aside-menu .menu-nav > .menu-item.menu-item-active > .menu-heading .menu-icon i,
.aside-menu .menu-nav > .menu-item.menu-item-active > .menu-link .menu-icon i,
.aside-menu .menu-nav > .menu-item.menu-item-open > .menu-heading .menu-arrow,
.aside-menu .menu-nav > .menu-item.menu-item-open > .menu-link .menu-arrow,
.aside-menu .menu-nav > .menu-item.menu-item-active > .menu-heading .menu-arrow,
.aside-menu .menu-nav > .menu-item.menu-item-active > .menu-link .menu-arrow,
.aside-menu .menu-nav > .menu-item:not(.menu-item-parent):not(.menu-item-open):not(.menu-item-here):not(.menu-item-active):hover > .menu-heading .menu-arrow,
.aside-menu .menu-nav > .menu-item:not(.menu-item-parent):not(.menu-item-open):not(.menu-item-here):not(.menu-item-active):hover > .menu-link .menu-arrow,
.aside-menu .menu-nav > .menu-item:hover > .menu-heading .menu-icon i,
.aside-menu .menu-nav > .menu-item:hover > .menu-link .menu-icon i
{
	color: <?= $this->Main->getStyle('aside-ic-active'); ?>;
}

/*Cor do ícone do menu lateral  DENTRO DE UMA CLASSE FILHO*/
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item > .menu-heading .menu-icon i,
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item > .menu-link .menu-icon i
{
	color: <?= $this->Main->getStyle('aside-sub-ic'); ?>;
}

/*Fundo do botão do menu lateral quando ativo/hover */
.aside-menu .menu-nav > .menu-item.menu-item-active > .menu-heading, .aside-menu .menu-nav > .menu-item.menu-item-active > .menu-link, .aside-menu .menu-nav > .menu-item.menu-item-open > .menu-heading, .aside-menu .menu-nav > .menu-item.menu-item-open > .menu-link,
.aside-menu .menu-nav > .menu-item:not(.menu-item-parent):not(.menu-item-open):not(.menu-item-here):not(.menu-item-active):hover, .aside-menu .menu-nav > .menu-item:not(.menu-item-parent):not(.menu-item-open):not(.menu-item-here):not(.menu-item-active):hover > .menu-heading, .aside-menu .menu-nav > .menu-item:not(.menu-item-parent):not(.menu-item-open):not(.menu-item-here):not(.menu-item-active):hover > .menu-link
{
	background-color: <?= $this->Main->getStyle('aside-bg-active'); ?>;
}

/*Cor do texto do menu lateral*/
.aside-menu .menu-nav > .menu-item > .menu-heading .menu-text,
.aside-menu .menu-nav > .menu-item > .menu-link .menu-text
{
	color: <?= $this->Main->getStyle('aside-tx'); ?>;
}

/*Texto do botão do menu lateral quando ativo/hover*/
.aside-menu .menu-nav > .menu-item.menu-item-active > .menu-heading .menu-text,
.aside-menu .menu-nav > .menu-item.menu-item-active > .menu-link .menu-text,
.aside-menu .menu-nav > .menu-item.menu-item-open > .menu-heading .menu-text,
.aside-menu .menu-nav > .menu-item.menu-item-open > .menu-link .menu-text,
.aside-menu .menu-nav > .menu-item:not(.menu-item-parent):not(.menu-item-open):not(.menu-item-here):not(.menu-item-active):hover > .menu-heading .menu-text,
.aside-menu .menu-nav > .menu-item:not(.menu-item-parent):not(.menu-item-open):not(.menu-item-here):not(.menu-item-active):hover > .menu-link .menu-text
{
	color: <?= $this->Main->getStyle('aside-tx-active'); ?>;

}

/*Cor do ícone do menu lateral DENTRO DE UMA CLASSE FILHO quando ativo/hover*/
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item.menu-item-active > .menu-heading .menu-icon i,
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item.menu-item-active > .menu-link .menu-icon i,
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item:not(.menu-item-parent):not(.menu-item-open):not(.menu-item-here):not(.menu-item-active):hover > .menu-heading .menu-icon i,
 .aside-menu .menu-nav > .menu-item .menu-submenu .menu-item:not(.menu-item-parent):not(.menu-item-open):not(.menu-item-here):not(.menu-item-active):hover > .menu-link .menu-icon i
{
	color: <?= $this->Main->getStyle('aside-sub-ic-active'); ?>;
}

/*Texto do botão do menu lateral DENTRO DE UMA CLASSE FILHO*/
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item > .menu-heading .menu-text,
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item > .menu-link .menu-text
{
	color: <?= $this->Main->getStyle('aside-sub-tx'); ?>;
}

/*Fundo do botão do menu lateral DENTRO DE UMA CLASSE FILHO quando ativo/hover */
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item:not(.menu-item-parent):not(.menu-item-open):not(.menu-item-here):not(.menu-item-active):hover > .menu-heading,
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item:not(.menu-item-parent):not(.menu-item-open):not(.menu-item-here):not(.menu-item-active):hover > .menu-link,
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item.menu-item-active > .menu-heading,
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item.menu-item-active > .menu-link{

	background-color: <?= $this->Main->getStyle('aside-sub-bg-active'); ?>;
}

/*Texto do botão do menu lateral DENTRO DE UMA CLASSE FILHO quando ativo/hover*/
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item:not(.menu-item-parent):not(.menu-item-open):not(.menu-item-here):not(.menu-item-active):hover > .menu-heading .menu-text,
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item:not(.menu-item-parent):not(.menu-item-open):not(.menu-item-here):not(.menu-item-active):hover > .menu-link .menu-text,
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item.menu-item-active > .menu-heading .menu-text,
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item.menu-item-active > .menu-link .menu-text
{
	color: <?= $this->Main->getStyle('aside-sub-tx-active'); ?>;
}
