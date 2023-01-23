import React, { Component, useState, useEffect } from "react";
import { IsEmpty, Map, GroupBy } from "react-lodash"

function List(props){


    const [data, setData] = useState([]);

    useEffect(() => {
        //?page[offset]=50&page[limit]=50
        //?filter[parent.meta.drupal_internal__target_id]=193
        //get top level taxonomies

      
        fetch(`/jsonapi/node/news_item?filter[field_award_code.meta.drupal_internal__target_id]=${props.awardCode}&sort=-field_active_date&page[size]=20&page[number]=1`)
            .then(response=>response.json())
            .then(data => {


                let newData = [];
                let newDat = {}

                data.data.forEach(function(el){

 
                    let title = el.attributes.title;
                    let date = el.attributes.field_active_date;
                    let year = date.split("-")[0];
                    
                    let winnernames= el.attributes.field_award_winner_names;
                    let temp = {
                        title: title,
                        date: date,
                        winnernames: winnernames,
                        id: el.attributes.drupal_internal__nid,
                        link: el.links.self.href,
                        year: year
                    }
                    if(newData[year]){
                        
                    } else {

                        newData[year] = Array();
                    }
                    if(newDat[year]) {
                        newDat[year].push(temp);
                    } else {
                        newDat[year] = [];
                        newDat[year].push(temp);
                    }

                    newData[year].push(temp)
                    

                    
                });


              

                
                setData(newData);
                
            });
            
        
        
      }, []);

      const getByValue = function(map, searchValue) {
        for (let [key, value] of map.entries()) {
          if (value === searchValue)
            return key;
        }
      }

      const sortByKey = function(array, key) {
        return array.sort(function(a, b) {
            var x = a[key]; var y = b[key];
            return ((x < y) ? -1 : ((x > y) ? 1 : 0));
        });
    }

    return (
        <div>
            <div>List for code: {props.awardCode}</div>
            <ul>
                {(data.length? data.map(item=>{


                    let key = getByValue(data, item);
                    console.log("jey", key)

                    return(
                        <li key={key} data-year={key} className="section">
                            <h3>{key}</h3>
                            <ul>
                            {item.map(it=>{

                                return(
                                    <li key={it.id}>
                                        <a href={it.link}>{it.title}</a><br />
                                        <span>{it.winnernames}</span>
                                    </li>
                                )
                            })}
                            </ul>
                            
                        </li>
                    )
                }) : "nope")}
            </ul>
        </div>
    )
}

export default List;