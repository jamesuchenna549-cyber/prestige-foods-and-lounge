async function getData() {

  const response = await fetch('test.php');

  const data = await response.text();

  document.body.innerHTML = data;

}

getData();
