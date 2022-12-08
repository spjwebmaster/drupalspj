import React from 'react';
import ReactDOM from 'react-dom/client';
import './index.css';
import App from './App';
import reportWebVitals from './reportWebVitals';

let target = document.getElementById('reactform')
let loc = window.location.href;
if(loc.toLowerCase().indexOf("/awards/form/")<0 && loc.toLowerCase().indexOf("/form/")>-1){
  target = document.querySelector(".form-item-main-category");
}

const root = ReactDOM.createRoot(target);
root.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);

// If you want to start measuring performance in your app, pass a function
// to log results (for example: reportWebVitals(console.log))
// or send to an analytics endpoint. Learn more: https://bit.ly/CRA-vitals
reportWebVitals();
