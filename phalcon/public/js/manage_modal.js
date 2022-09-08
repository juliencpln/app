
function manageModal(vue, data){
	vue.modalOpen = true;
	// Passage des données à l'application
	vue.data = data;

	// Gestion du format de date
	vue.data.birthday = getFormattedDate(vue.data.birthday);
	vue.data.first_day = getFormattedDate(vue.data.first_day);
};

function clearForm(vue){

	// Remise à 0 de toutes les valeurs
   Object.keys(vue.data).forEach(function(key) {
   		vue.data[key] = '';
	});

};

// Ajout de la logique à l'application vue
const mixin_manage_modal = {

	data() {
        let modalOpen = false;
        let confirmDelete = false;
        const countryList = getCoutryList();
		let companiesList = [];
		let clientsList = [];
		let providersList = [];
		let employeesList = [];
		let productsList = [];
        let data = [];
        let messageError;

		return {
		    modalOpen,
		    confirmDelete,
		    countryList,
		    companiesList,
		    employeesList,
		    clientsList,
			providersList,
			productsList,
		    data,
		    messageError,
		}
    },

     watch: {
	    // Surveille la fermeture de la modale
	    modalOpen(newState, oldState) {
		      if (newState == false) {
		      	// Enleve la confirmation de suppression
		        this.confirmDelete = false;
		        this.messageError = false;
		        clearForm(this);
		      }
		      else
		      {
		      	// Gestion des selecteurs (valeur par défaut)
		      	if(!this.data.id){
		      		this.data.country = "";
		      		this.data.id_company = "";
		      		this.data.id_client = "";
		      		this.data.id_provider = "";
		      		this.data.id_product = "";
		      		this.data.id_employee = "";
		      	}
		      	else
		      	{
		      		if(source == '/employees' || source == '/transactions'){
			      		fetch("/api/employees/id_company="+this.data.id_company)
			            .then(response => response.json())
			            .then((result) => {
			            	this.employeesList = result.data;
			            });
		        	}
		      	}
				
				if(source == '/employees' || source == '/transactions'){
					fetch("api/companies")
		            .then(response => response.json())
		            .then((result) => {

		            	Object.keys(result.data).forEach(key => {
						  result.data[key].balance = parseFloat(result.data[key].balance).toLocaleString('fr')
						});

		            	this.companiesList = result.data;
		            });
				}	

				if(source == '/transactions'){
		            fetch("api/clients")
		            .then(response => response.json())
		            .then((result) => {
		            	this.clientsList = result.data;
		            });

		            fetch("api/providers")
		            .then(response => response.json())
		            .then((result) => {
		            	this.providersList = result.data;
		            });

		            fetch("api/products")
		            .then(response => response.json())
		            .then((result) => {
		            	this.productsList = result.data;
		            });
		        }
		    }
	    },
	},

  	methods: {
  		changeCompany: function(){
		    
		    this.data.id_employee = "";

  			fetch("/api/employees/id_company="+this.data.id_company)
            .then(response => response.json())
            .then((result) => {
            	this.employeesList = result.data;
            });
  		},
  		changeClient: function(){
		    this.data.id_provider = "";
  		},
  		changeProvider: function(){
  			this.data.id_client = "";
  		},
  		onSubmit(e){
            e.preventDefault();

			let request = {};
			let method = '';
			let apiRoute = '';

			// Récupération de toutes les entrée du formulaire
			Object.keys(this.data).forEach(key => {
			 	if((key == 'id_client' ||  key == 'id_provider') && this.data[key] == ''){
			 		//id_client et id_provider à null pour être correctement insérés en base de données (foreign_key)
			 		request[key] = null;
				}
				else
				{
					request[key] = this.data[key];
				}
			});

			// Détermine si c'est une création ou une modification
		    if(!this.data.id){
		    	// Création
		    	method = "POST";
		    	apiRoute = 'api'+source;

		    }
		    else
		    {
		    	// Modification
		    	method = "PUT";
		    	apiRoute = 'api'+source+'/'+this.data.id;
		    }

			fetch(apiRoute, {
			  method: method,
			  body: JSON.stringify(request),
			  headers: {"Content-type": "application/json; charset=UTF-8"}
			})
			.then(response => response.json())
    		.then((result) => {
				if(result.last_id){
					this.modalOpen = false;
					
					// Récupération du dernier id
					request['id'] = result.last_id;
					if(method == 'POST'){
						// Ajout du nouveau résultat au tableau
						fetchedData.unshift(request);
					}
					else
					{	
						// Recherche et remplace le résultat mis à jour
						let test = fetchedData.find((result,index) => {
							if(result.id === request['id']){
								fetchedData[index] = request;
							};
						});
					}
					// Rendu du tableau avec la donnée ajoutée
					grid.updateConfig({
						data: fetchedData,
					}).forceRender();
				}
				else
				{
					this.messageError = result.message;
				}

			});
        },
	    deleteData: function(id) {
	  		
	  		// Recherche de la valeur à supprimer dans les résultats remontés par l'API
	  		var index = fetchedData.findIndex(el => el.id == id);
	  		if (index > -1) {
			  fetchedData.splice(index, 1);
			}

			// Rendu du tableau sans la donnée supprimée
			grid.updateConfig({
				data: fetchedData,
			}).forceRender();

			// Appel API pour supprimer la donnée en base
	        fetch('api'+source+'/'+id, { method: 'DELETE' })
    		.then(() => {
    			// Reset les valeurs modale et confirmation de suppresion par défaut
    			this.modalOpen = false;
    			this.confirmDelete = false;
    		});
	    }
	}
}

