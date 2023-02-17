
	const VueTableRowComponent = {

		props: ['row', 'foot'],

		template: `<tr scope="row" :class="foot ? 'table-secondary' : ''">
			<td v-for="cell in row.cells" :class="['text-' + cell.align, 'align-' + cell.alignVertical]" :style="cell.width ? 'width:' + cell.width : ''" :colspan="cell.colspan">
				<template v-if="typeof cell.content === 'string'">
				<span v-html="cell.content"></span>
				</template>
				<template v-else>
					{{ cell.content }}
				</template>
			</td>
		</tr>`

	}

	const VueTableComponent = {

		components: {
			'vue-table-row-component': VueTableRowComponent
		},

		props: ['custom'],

		inject: ['table'],

		template: `<div class="table-responsive">
			<template v-if="table.hasPagination">
				<p class="text-muted" v-if="table.dynamic.items">Visualizando {{ table.pagination.totalResults }} de {{ table.pagination.size }} item(ns)</p>
			</template>
			<template v-else>
				<p class="text-muted" v-if="table.dynamic.items">Visualizando {{ table.dynamic.items.length }} item(ns)</p>
			</template>
			
			<table class="table table-hover">
				<thead>
					<tr>
						<template v-for="title in table.tableTitles">
							<th scope="col" :key="title.id" class="align-middle" :class="['text-' + title.align, title.sortable ? ['cursor-pointer', 'btn-hover-primary'] : '', title.id == table.sortingItem ? 'text-primary' : '' ]" @click="table.sortColumn(title);"  :style="title.width ? 'width:' + title.width : ''">
								<span :class="title.sortable ? 'text-hover-primary' : ''">{{title.text}}</span>
								<i class="fas fa-sort-up" v-if="title.sortable && title.id == table.sortingItem && table.sortingOrder == 'desc'"></i>
								<i class="fas fa-sort-down" v-if="title.sortable && title.id == table.sortingItem && table.sortingOrder == 'asc'"></i>
							</th>
						</template>
					</tr>
				</thead>
				<tbody>				
					<template v-if="custom">
						<slot></slot>
					</template>
					<template v-else>
						<template v-if="!custom && table.tableItems.length > 0">
							<vue-table-row-component v-for="row in table.tableItems" :key="row.id" :row = "row" v-if="table.tableItems !== null || table.tableItems.length == 0"></vue-table-row-component>
						</template>
						<tr v-if="!custom && table.tableItems.length < 1">
							<td :colspan="table.tableTitles.length" class="text-center">
								<span class="text-muted">
									{{ table.empty }}
								</span>
							</td>
						</tr>
					</template>
				</tbody>

				<tfoot v-if="table.tableFooter">
					<vue-table-row-component :row="table.tableFooter" :foot="true"></vue-table-row-component>
				</tfoot>

			</table>

	
			<div class="d-flex justify-content-between align-items-center flex-wrap" v-if="table.hasPagination && table.tableItems.length > 0">
				<div class="d-flex flex-wrap py-2 mr-3">
					
					<button class="btn btn-icon btn-sm btn-light mr-2 my-1" @click="table.firstPage" :disabled="table.isFirstPage" :class="table.isFirstPage ? 'disabled' : ''">
						<i class="ki ki-bold-double-arrow-back icon-xs"></i>
					</button>
					<button class="btn btn-icon btn-sm btn-light mr-2 my-1" @click="table.previousPage" :disabled="table.isFirstPage" :class="table.isFirstPage ? 'disabled' : ''">
						<i class="ki ki-bold-arrow-back icon-xs"></i>
					</button>

					<template v-for="page in table.getPageNumbers()">
						<button class="btn btn-icon btn-sm border-0 btn-light mr-2 my-1" :key="page" @click="table.goToPage(page)" :class="table.getCurrentPage == page ? 'btn-hover-primary active' : '' " v-if="!table.isSeparator(page)">{{page}}</button>

						<button class="btn btn-icon btn-sm border-0 btn-light mr-2 my-1 disabled" :key="page" disabled v-else>{{page}}</button>
					</template>

					<button class="btn btn-icon btn-sm btn-light mr-2 my-1" @click="table.nextPage" :disabled="table.isLastPage" :class="table.isLastPage ? 'disabled' : ''">
						<i class="ki ki-bold-arrow-next icon-xs"></i>
					</button>
					<button class="btn btn-icon btn-sm btn-light mr-2 my-1" @click="table.lastPage" :disabled="table.isLastPage" :class="table.isLastPage ? 'disabled' : ''">
						<i class="ki ki-bold-double-arrow-next icon-xs"></i>
					</button>
				</div>
				<div class="d-flex align-items-center py-3">
					<select class="form-control form-control-sm font-weight-bold mr-4 border-0 bg-light" style="width: 75px;" v-model="table.pagination.itemsPerPage">
						<option v-for="option in table.pagination.perPageOptions" :value="option" :key="option">{{ option }}</option>
					</select>
				</div>
			</div>
		</div>`
	}

	const TableView = function(options)
	{
		options = (typeof options == 'object') ? options : {};

		this.mixins = [new VueDynamic(options), new VuePagination(options)]

		this.data = function(){

			return {
				sortingItem: false,
				sortingOrder: false,
				empty: options.empty ? options.empty : 'Nenhum item encontrado',
				footer: options.footer ? options.footer : []
			}
		}

		this.components = {

			'table-component': VueTableComponent
		}

		this.provide = function(){

			return {'table': this};
		}

		this.methods = {

			sortColumn: function(title)
			{
				if(!title.sortable) return;

				let columnId = title.id;
				let order = (this.sortingItem != columnId) ? 'asc' : (this.sortingOrder == 'asc' ? 'desc' : false);
				
				this.filter('order', order ? [columnId, order] : false);
				this.sortingItem = order ? columnId : false;
				this.sortingOrder = order;
			}

		}

		this.computed = {

			tableFooter: function(){

				let cells = [];

				for(let i in this.footer)
				{
					let col = this.footer[i];
					
					let value = col.content;

					if(typeof value == "function")
						value = value();

					else if(col.mask && !is.undefined(Maskr) && !is.undefined(Maskr[col.mask]))
						value = Maskr[col.mask](value);

					else if(col.mask && !is.undefined(Maskr))
						value = Maskr.mask(value, col.mask);

					cells.push({
						align: col.align ? col.align : (options.align ? options.align : 'center'),
						alignVertical: col.alignVertical ? col.alignVertical : (options.alignVertical ? options.alignVertical : 'top'),
						content: value,
						width: col.width ? col.width : null,
						colspan: col.colspan ? col.colspan : null
					});
									
				}


				return {cells: cells};
			},

			tableItems: function(){

				let rows = [];

				for(let i in this.getData)
				{
					let item = this.getData[i],
						cells = [];

					for(let c in options.columns)
					{
						let col = options.columns[c];

						let value = col.item && item[col.item] ? item[col.item] : (col.default ? col.default : '');

						if(typeof value == "function")
							value = value();

						if(is.function(col.render))
							value = col.render(value, item);

						else if(col.mask && !is.undefined(Maskr) && !is.undefined(Maskr[col.mask]))
							value = Maskr[col.mask](value);

						else if(col.mask && !is.undefined(Maskr))
							value = Maskr.mask(value, col.mask);

						cells.push({
							align: col.align ? col.align : (options.align ? options.align : 'center'),
							alignVertical: col.alignVertical ? col.alignVertical : (options.alignVertical ? options.alignVertical : 'top'),
							content: value,
							width: col.width ? col.width : null
						});
					}

					rows.push({id: i, cells: cells});					
				}

				return rows;

			},

			tableTitles: function(){

				let arr = [];

				for(let i in options.columns)
				{
					let col = options.columns[i];
					let sortable = !is.undefined(col.sortable) ? col.sortable : (!is.undefined(options.sortable) ? options.sortable : false);

					arr.push({
						id: col.item ? col.item : i,
						text: col.title ? col.title : col.item,
						sortable: sortable ? true : false,
						align: col.titleAlign ? col.titleAlign : (options.titleAlign ? options.titleAlign : 'center'),
						alignVertical: col.alignVertical ? col.alignVertical : (options.alignVertical ? options.alignVertical : 'top'),
						width: col.width ? col.width : null
					});

					if(is.string(sortable))
					{
						this._filter('order', [col.item, sortable]);
						this.sortingItem = col.item;
						this.sortingOrder = sortable;
					}
				}

				return arr;

			}

		}
	}