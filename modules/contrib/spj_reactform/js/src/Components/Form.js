import React, { Component, useState, useEffect } from "react";
import Maincat from "./Maincat";


function Form(props){

    let awardName = "sdx";
    let loc = window.location.href;
    const [awardCode, setAwardCode] = useState();
    
    const [mainCatSelected, setMainCatSelected] = useState("");
    
    const [subCatSelected, setSubCatSelected] = useState("");
    

    const mainCatChange=(e)=> {
        console.log("change", e.target.value)
        setMainCatSelected(e.target.value);
    }
    const subCatChange=(e)=> {
        console.log("change", e.target.value)
        setSubCatSelected(e.target.value);
    }

    useEffect(() => {
        //?page[offset]=50&page[limit]=50
        //?filter[parent.meta.drupal_internal__target_id]=193
        //get top level taxonomies

      
        fetch(`/jsonapi/taxonomy_term/award_submission_categories?filter[parent.meta.drupal_internal__target_id]=null`)
            .then(response=>response.json())
            .then(data => {

                data.data.forEach(function(el){

                    if(el.attributes.name.toLowerCase()==awardName){
                        setAwardCode(el.attributes.drupal_internal__tid);
                    }
                });
            });
            
        
        
      }, [awardCode]);

    return (<div>
       <h2>{awardName} {awardCode}</h2>

        <Maincat change={mainCatChange} mainCatSelected={mainCatSelected} awardCode={awardCode} />

    </div>)
}
export default Form;