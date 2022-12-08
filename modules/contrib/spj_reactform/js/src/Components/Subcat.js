import React, { Component, useState, useEffect } from "react";

function Subcat(props){

    const [subCat, setSubCat] = useState([]);
    const [subCatSelected, setSubCatSelected] = useState("");
    const [subCatDesc, setSubCatDesc] = useState("");

    const subCatChange=(e)=> {
        //console.log("change", e.target.value)
        setSubCatSelected(e.target.value);
    }


    useEffect(() => {
        fetch(`/jsonapi/taxonomy_term/award_submission_categories?filter[parent.meta.drupal_internal__target_id]=${props.mainCatSelected}`)
        .then(response=>response.json())
        .then(secondary => {
            //console.log("secondary: ", secondary.data);
            setSubCat(secondary.data);
            if(subCatSelected==""){
                setSubCatSelected(secondary.data[0].attributes.drupal_internal__tid)
            }
            let filtered = secondary.data.filter(t=>t.attributes.drupal_internal__tid == subCatSelected);
            //console.log("filtered secondary", "selected:", subCatSelected, secondary.data, filtered)
            if(filtered.length>0){
                setSubCatDesc((filtered[0].attributes.description?filtered[0].attributes.description.value:""))
            } else{
                setSubCatDesc("")
            }

        });
    }, [subCatSelected, props.mainCatSelected]);

    return(
        <div>
            <div className="well">
                <label>Sub Category</label><br />
                
                <select name="subCategory" title="mainCategory" className="form-control" onChange={subCatChange} value={subCatSelected}>
                    <option value="-">-Select-</option>
                    {(subCat? subCat.map(item=>{

                        return (
                            <option key={item.id} value={item.attributes.drupal_internal__tid}>{item.attributes.name}</option>
                        )

                        }):"...")}
                </select>
                <br />
                <div className="description" dangerouslySetInnerHTML={{__html: (subCatDesc?subCatDesc:"")}}></div>
            </div>
        </div>
    )
}

export default Subcat;