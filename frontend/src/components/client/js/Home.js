import React, { Component } from 'react';
import "../../css/home.css";
import axios from 'axios';

class Home extends Component {
  constructor(props) {
    super(props);
    
    this.state = {
            host: "http://localhost:8000",
            filters:    [],
            categories: [],
            vehicles:   [],
            agencies:   [],
            models:     [],
            brands:     []

    }
    
    this.agencyFilter = this.agencyFilter.bind(this);
    this.upperFilter = this.upperFilter.bind(this);
    this.handleFilterClick = this.handleFilterClick.bind(this); // this function used to handle the click the filter on the left of the page 
    this.categoryFilter = this.categoryFilter.bind(this);
    this.carCards = this.carCards.bind(this);
    this.modelFilter = this.modelFilter.bind(this);
    this.pagination = this.pagination.bind(this);
    this.delete = this.delete.bind(this); 

     this.filters = ( elements , attribute , filterTitle) =>{ 
        const filterElements = [];
         
     for (var i in elements) {
       if (!Number.isInteger(parseInt(i))) { //  since  every response that concerns a collection contains pagination 
        // this condition allow excluding the page , link , limit attribute 
        break;
      }
      var element = elements[i];
       var elementUI = <div class="custom-control custom-checkbox fade show"   key={element[attribute]}>
        <input type="checkbox" class="custom-control-input" id={element[attribute]} name="checkbox-stacked"
          onChange={(e) => this.handleFilterClick(e)} />
        <label class="custom-control-label text-monospace" for={element[attribute]} >{element[attribute]}</label>
      </div>
     filterElements.push(elementUI);
    };
    return (
      <div className="filter agencyFilter rounded">
        <div className="title">{filterTitle} :  </div>
        <div className="components">
          {filterElements}
        </div>
      </div>
    );
  }


  }
    componentDidMount() {
    $('.toast').toast('show');

    try {
      axios.get(this.state.host + "/public/agencies")  // get  the available agencies
        .then(res => {
          this.setState({ agencies: JSON.parse(JSON.stringify(res.data)) });
        });
      axios.get(this.state.host + "/public/categories")  // get the cavailable categories
        .then(res => {
          this.setState({ categories: JSON.parse(JSON.stringify(res.data)) });
        });
        axios.get(this.state.host + "/public/brands")  // get the cavailable categories
        .then(res => {
          this.setState({ brands: JSON.parse(JSON.stringify(res.data)) });
        });
        axios.get(this.state.host + "/public/models")  // get the cavailable categories
        .then(res => {
          this.setState({ models: JSON.parse(JSON.stringify(res.data)) });
        });


    } catch (e) {
      console.log(e);
    }
  }
  // handling filter Click 
  handleFilterClick(event){ 
     const filters = this.state.filters;
     const filtername= event.currentTarget.id ;
      if(  event.currentTarget.checked) {

      filters.push(filtername);  // on each click a filter is added to the array of filters  in the state
    } else {
     filters.pop(filtername);
    }
    this.setState({
      filters : filters
    })
  
  }
  
 
 
  // filters
  agencyFilter() {
 
       return  this.filters(this.state.agencies ,"agency_code","Agency"); 
  }  
  categoryFilter() {
 
    return  this.filters(this.state.categories ,"name_","Category"); 

  }
  modelFilter() {
 
     return  this.filters(this.state.models ,"name_","Model"); 

  }
  brandFilter() {
 
    return  this.filters(this.state.brands ,"name_","Brand"); 

   }
   delete(element , e){ 
     console.log(e.currentTarget);
      e.currentTarget.remove();
   }
  upperFilter( ) {  // this part contains the filters selected by the left bar 
   const filters = this.state.filters; 
  const currentFilters = []; 
    filters.forEach((filter) =>{

        var filterUI = 
          <div className="toast rounded-pill fade show" role="alert" aria-live="polite"
           aria-atomic="true" data-autohide="false"   onClick={ (e)=>{ this.delete(this , e)}}>
          <div role="alert" aria-live="assertive" aria-atomic="true" 
          className="filter-name text-monospace"  >  {filter}</div>
          <button type="button" class="ml-2 mb-1 close" aria-label="Close">
            <span aria-hidden="true" 
           >

            &times;</span>
          </button>
        </div>
       currentFilters.push(filterUI);
     }); 
     // making sure that the filter toast are visible
     return (
      <div className="filter-buttons rounded">
        <div className="filter-title"> Filter :</div>
        <div className="upper-filters-container">
          {currentFilters}
        </div> 
      </div>
      
    );

  }
  carCards() { // displaying the cars' info for each one 

  return (
      <div className="car-cards">
        <img src="data:image/jpeg;base64,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"
          className="rounded-pill" />
        <div className="information">
          <div className="left">
            <div className="carTitle"> Title
                  <div className="category-model text-monospace">category model </div> </div>
            <div className="secondaryInfo">
              <div> state :new</div>
              <div> gearbox:automatic</div>
              <div> max passengers: 4 </div>
              <div> max suitcase: 5</div>
            </div>
          </div>
          <div className="right">
            <i class="fas fa-plus-circle"></i>
            <div className="price"> <p> Rental : 3000 DA   </p>
              <p>Inssurance : 300 DA</p>
            </div>
            <div className="btn-group dropright">
              <button type="button" className="btn  dropdown-toggle" id="vehicle-agency" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false"> Agency  </button>
              <div className="dropdown-menu dropdown-menu-left">
                <div className="dropdown-item">   <i class="fas fa-building"></i>
                  <div className="text-monospace"> AgencyCity</div> </div>
                <div className="dropdown-item">   <i class="fas fa-phone-alt"></i> <div className="text-monospace"> 021547986</div> </div>
                <div className="dropdown-item">  <i class="fas fa-at"></i> <div className="text-monospace"> agency@gmail.com</div> </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    );
  }

  pagination() { // the pagination used to display the car cards 
    return (
      <ul className="pagination">
        <li className="page-item disabled" >  <a className="page-link" href="#"> previous</a></li>
        <li className="page-item"> <a className="page-link" href="#"> 1</a></li>
        <li className="page-item"> <a className="page-link" href="#"> 2</a></li>
        <li className="page-item"> <a className="page-link" href="#">Next </a></li>
      </ul>
    );
  }
  render() {
    $('.toast').toast('show');

    return (
      <div className="container">
        <div className="row">
          <div className="col-sm-3" id="filters">
            <this.agencyFilter></this.agencyFilter>
              <this.categoryFilter></this.categoryFilter>
            <this.modelFilter></this.modelFilter>
           </div>
          <div className="col-sm-9">
            <this.upperFilter></this.upperFilter>
            <this.carCards></this.carCards>`                                                                                                                                     `
                 <this.pagination></this.pagination>
          </div>
        </div>
      </div>

    );
  }
}
export default Home; 