let grid;
let fetchedData;
let columns;
// Remplissage des tableau pour toutes les entitées
function tableFill(vue){

	source = window.location.pathname;
	vue.source = source;

	grid = new gridjs.Grid({

	// Récupération des données de l'entité 
	data: () => {
        return new Promise(resolve => {
            fetch("api"+source)
            .then(response => response.json())
            .then((result) => {
              // return data;

	          if (!fetchedData) {
					fetchedData = result.data;
	                columns = result.columns;
	                // Traduction des en-têtes
	                columns.forEach(function(item,i){
		             	switch (item) {
			             	case 'id':
							columns[i] = {id: 'id', name:'N°'};
							break;
							case 'name':
							columns[i] = {id: 'name', name:'Nom'};
							break;
							case 'balance':
							columns[i] = {id: 'balance', name:'Solde', formatter: (cell) => parseFloat(cell).toLocaleString('fr')+'€'};
							break;
							case 'country':
							columns[i] = {id: 'country', name:'Pays'};
							break;
							case 'price':
							columns[i] = {id: 'price', name:'Prix', formatter: (cell) => parseFloat(cell).toLocaleString('fr')+'€'};
							break;
							case 'tax':
							columns[i] = {id: 'tax', name:'Taxe', formatter: (cell) => `${cell}%`};
							break;
							case 'stock':
							columns[i] = {id: 'stock', name:'Quantité'};
							break;
							case 'address':
							columns[i] = {id: 'address', name:'Adresse'};
							break;
							case 'birthday':
							columns[i] = {id: 'birthday', name:'Anniversaire', formatter: (cell) => getFormattedDate(cell)};
							break;
							case 'first_day':
							columns[i] = {id: 'first_day', name:'Date d\'entrée', formatter: (cell) => getFormattedDate(cell)};
							break;
							case 'id_company':
							columns[i] = {id: 'id_company', name:'N° Entreprise'};
							break;
							case 'id_client':
							columns[i] = {id: 'id_client', name:'N° Client' , formatter: (cell) => getTypeAction(cell)}
							break;
							case 'id_provider':
							columns[i] = {id: 'id_provider', name:'N° Fournisseur' , formatter: (cell) => getTypeAction(cell)}
							break;
							case 'id_product':
							columns[i] = {id: 'id_product', name:'N° Produit' };
							break;
							case 'quantity_product':
							columns[i] = {id: 'quantity_product', name:'Quantité'};
							break;
							case 'id_employee':
							columns[i] = {id: 'id_employee', name:'N° Référent'};
							break;						
						}
				    });

					grid.updateConfig({
						columns: columns,
						data: fetchedData,
					}).forceRender();
				}
            })
          });
        },

        // Filtre de recherche
   		search: {
		    enabled: true,
		    ignoreHiddenColumns:false,
		    selector:true,
		},

		// Définition de la langue
		language: {
		    'search': {
		        'placeholder': 'Recherche'
		    },
		    'pagination': {
		        'previous': 'Précédent',
		        'next': 'Suivant',
		        'showing': 'Affichage des résultats',
		        "of": 'sur',
		        "to": 'à',
		        'results': () => 'résultats'
		    },
		    'loading': 'Chargement...',
		    'noRecordsFound': 'Aucun résultat',
		},

		// Ordre sur les colonnes (asc/desc)
		sort: true,

		// Pagination
		pagination: {
		    limit: 10
		},
	}).render(document.getElementById("wrapper"));

	 grid.on('rowClick', (...args) =>{
	 	if(user_session.is_admin == 1){
	      var sel = getSelected();
	      if (sel === "") {
	          if(args[0].target.type == undefined ){

	            fetch("api"+source+"/"+args[1]._cells[0].data)
	            .then(response => response.json())
	            .then((result) => {
	            	 manageModal(vue, result);
	            });
	          }
	      }
	    }
    });

	grid.on('ready', (...args)=>{
		//bug boutton multiple
		if(user_session.is_admin == 1){
	 		addButton();
	 	}

	});

	function addButton() {
		var head = document.getElementsByClassName('gridjs-head');
	 	var el = head[0];

		var buttonAdd = '<a class="w-full mt-3 sm:mt-0 inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto cursor-pointer" id="addButton"> <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-6 w-6 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"> <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /> </svg> Ajouter </a>';

		var elementExists = document.getElementById("addButton");
		if(elementExists == null){
			el.insertAdjacentHTML('beforeend', buttonAdd);
		}

	  	document.getElementById("addButton").addEventListener("click", function() {
		  	vue.modalOpen = true;
		});
	}

	function getSelected() {
        if (window.getSelection) {
            return window.getSelection().toString();
        } else if (document.getSelection) {
            return document.getSelection().toString();
        } else {
            var selection = document.selection && document.selection.createRange();
            if (selection.text) {
                return selection.text.toString();
            }
        }
        return;
    }
}

// On ajoute la logique à l'application vue
const mixin_table_filler = {

	async mounted() {
    	// Remplissage des tableaux
        tableFill(this);     

    },
}

    

