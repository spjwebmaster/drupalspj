import logo from './logo.svg';
import './App.css';
import List from './List';

function App(props) {
  return (
    <div>
      <List awardCode={props.awardCode} />
    </div>
  );
}

export default App;
