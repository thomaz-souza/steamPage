const PaginationComponent = {

	template: `<div class="d-flex justify-content-between align-items-center flex-wrap" v-if="$root.hasPagination && !$root.isEmpty">
		<div class="d-flex flex-wrap py-2 mr-3">
			
			<button class="btn btn-icon btn-sm btn-light mr-2 my-1" @click="$root.firstPage" :disabled="$root.isFirstPage" :class="$root.isFirstPage ? 'disabled' : ''">
				<i class="ki ki-bold-double-arrow-back icon-xs"></i>
			</button>
			<button class="btn btn-icon btn-sm btn-light mr-2 my-1" @click="$root.previousPage" :disabled="$root.isFirstPage" :class="$root.isFirstPage ? 'disabled' : ''">
				<i class="ki ki-bold-arrow-back icon-xs"></i>
			</button>

			<template v-for="page in $root.getPageNumbers()">
				<button class="btn btn-icon btn-sm border-0 btn-light mr-2 my-1" :key="page" @click="$root.goToPage(page)" :class="$root.getCurrentPage == page ? 'btn-hover-primary active' : '' " v-if="!$root.isSeparator(page)">{{page}}</button>

				<button class="btn btn-icon btn-sm border-0 btn-light mr-2 my-1 disabled" :key="page" disabled v-else>{{page}}</button>
			</template>

			<button class="btn btn-icon btn-sm btn-light mr-2 my-1" @click="$root.nextPage" :disabled="$root.isLastPage" :class="$root.isLastPage ? 'disabled' : ''">
				<i class="ki ki-bold-arrow-next icon-xs"></i>
			</button>
			<button class="btn btn-icon btn-sm btn-light mr-2 my-1" @click="$root.lastPage" :disabled="$root.isLastPage" :class="$root.isLastPage ? 'disabled' : ''">
				<i class="ki ki-bold-double-arrow-next icon-xs"></i>
			</button>
		</div>
		<div class="d-flex align-items-center py-3">
			<select class="form-control form-control-sm font-weight-bold mr-4 border-0 bg-light" style="width: 75px;" v-model="$root.pagination.itemsPerPage">
				<option v-for="option in $root.pagination.perPageOptions" :value="option" :key="option">{{ option }}</option>
			</select>
		</div>
	</div>`
};

Vue.component('pagination', PaginationComponent);

const VuePagination = function (options) {
	
	options = (typeof options == 'object') ? options : {};

	let itemsPerPage = (options.itemsPerPage) ? options.itemsPerPage : 10;
	let perPageOptions = (options.perPageOptions) ? options.perPageOptions : [10,25,50,100];

	//Verifica se a quantidade atual de itens está contida na lista de opções
	if(perPageOptions.indexOf(itemsPerPage) == -1)
	{
		for(let o in perPageOptions)
			if((itemsPerPage < perPageOptions[o] && o == 0) || perPageOptions[o] > itemsPerPage || o == perPageOptions.length-1)
			{
				perPageOptions.splice(o, 0, itemsPerPage);
				break;
			}		
	}

	this.data = function () {

		return {
			pagination: {
				itemsPerPage: itemsPerPage,
				currentPage: (options.currentPage) ? options.currentPage : 1,
				perPageOptions: perPageOptions,
				resultsFrom: 0,
				resultsTo: 0,
				size: 0,
				totalPages: 0,
				totalResults: 0
			}
		}
	}

	
	this.methods = {
		
		goToPage: function(page){
			if(this.pagination.currentPage == page) return;
			this.pagination.currentPage = page;
		},
		nextPage: function(){
			if(this.pagination.currentPage  < this.pagination.totalPages)
				this.goToPage(this.pagination.currentPage +1);
		},
		previousPage: function(){
			if(this.pagination.currentPage  > 1)
				this.goToPage(this.pagination.currentPage -1);
		},
		firstPage: function(){
			this.goToPage(1);
		},
		lastPage: function(){
			this.goToPage(this.pagination.totalPages);
		},
		shiftPage: function(qtd){
			let page = this.pagination.currentPage + qtd,
				total = this.pagination.totalPages;
			
			if(page > total) page = total;
			else if(page < 1) page = 1;

			this.goToPage(page);
		},
		getPageNumbers: function(delta, separator) {

			if(!this.hasPagination)
				return [];

			delta = (typeof delta == 'number') ? delta : (options.delta ? options.delta : 5);

			let curpage = this.pagination.currentPage 
				last = this.pagination.totalPages;

			separator = (typeof separator == 'string') ? separator : (options.separator ? options.separator : false);

		    let left = curpage - delta,
		        right = curpage + delta + 1,
		        range = [],
		        rangeSeparator = [],
		        l;

		    for (let i = 1; i <= last; i++) {
		        if ( (separator && (i == 1 || i == last)) || (i >= left && i < right)) {
		            range.push(i);
		        }
		    }

		    if(typeof separator != 'string')
				return range;
		    
		    for (let i of range) {
		        if (l) {
		            if (i - l === 2) {
		                rangeSeparator.push(l + 1);
		            } else if (i - l !== 1) {
		            	rangeSeparator.push(separator);
		            }
		        }
		        rangeSeparator.push(i);
		        l = i;
		    }
		    return rangeSeparator;
		},

		isSeparator: function(val)
		{
			let separator = options.separator ? options.separator : false;
			return val == separator;
		}

	}
	
	this.computed = {

		isFirstPage: function(){
			return this.hasPagination && this.pagination.currentPage == 1;
		},

		isLastPage: function(){
			return this.hasPagination && this.pagination.currentPage == this.pagination.totalPages;
		},

		getCurrentPage: function(){
			return this.pagination.currentPage;
		}
	}

}