import React, { Component, useState, useEffect } from "react";
import Subcat from "./Subcat";

function Maincat(props){

    const [mainCat, setMainCat] = useState([]);
    const [mainCatSelected, setMainCatSelected] = useState("");
    const [mainCatDesc, setMainCatDesc] = useState("");
    const [subCatSelected, setSubCatSelected] = useState("");
    const mainCatChange=(e)=> {
        //console.log("change", e.target.value)
        setMainCatSelected(e.target.value);
    }
    
    useEffect(() => {
    
        //console.log("props.awardCode", props.awardCode)
        if(props.awardCode){
            fetch(`/jsonapi/taxonomy_term/award_submission_categories?filter[parent.meta.drupal_internal__target_id]=${props.awardCode}`)
            .then(response=>response.json())
            .then(data => {
                //console.log("all top level entries for moe", data.data);
                setMainCat(data.data);
                if(mainCatSelected==""){
                    setMainCatSelected(data.data[0].attributes.drupal_internal__tid);
                } 
                let filtered = data.data.filter(t=>t.attributes.drupal_internal__tid == mainCatSelected);
                //console.log("maincat filter", "selected:", mainCatSelected, "data", data.data, filtered)
                if(filtered.length>0){
                    if(filtered[0].attributes.description){
                        setMainCatDesc(filtered[0].attributes.description.value)
                    } else {
                        setMainCatDesc("")
                    }
                }
            });
        }
        

    }, [props.awardCode,mainCatSelected]);

    return(
        <div>
            <div className="well">
                <label>Main Category</label><br />
  
                    <select name="mainCategory" title="mainCategory" className="form-control" onChange={mainCatChange} value={mainCatSelected}>
                    {(mainCat? mainCat.map(item=>{

                        return (
                            <option key={item.id} value={item.attributes.drupal_internal__tid}>{item.attributes.name}</option>
                        )
                        
                    }):"...")}
                </select>
                <br />
                <div className="description" dangerouslySetInnerHTML={{__html: (mainCatDesc?mainCatDesc:"")}}></div>
                <br />
            </div>

            <Subcat mainCatSelected={mainCatSelected} />
        </div>
    )
}

export default Maincat;