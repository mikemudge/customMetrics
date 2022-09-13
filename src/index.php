<?

$url = strtok($_SERVER["REQUEST_URI"], '?');
if ($url == "/apis/custom.metrics.k8s.io/v1beta1") {
	// TODO not sure if any of this matters or not?
	$health = [
		"kind" => "APIResourceList",
  		"apiVersion" => "v1",
  		"groupVersion" => "metrics.k8s.io/v1beta1",
		"resources" => [
		    [
		      "name" => "services",
		      "singularName" => "",
		      "namespaced" => true,
		      "kind" => "PodMetrics",
		      "verbs" => [
		        "get",
		        "list"
		      ]
		    ]
		  ]
	];
	echo json_encode($health);
	exit();
}
$parts = explode("/", $url);

// Expecting something like /namespaces/<NAMESPACE>/services/<NAME_OF_CUSTOM_METRICS_SERVICE>/<METRIC_NAME>
if (count($parts) < 9 || $parts[0] != "" || $parts[4] != "namespaces" || $parts[6] != "services") {
	echo "Invalid path " . $_SERVER['REQUEST_URI'] . "<br>";
	exit();
}

// Expecting /apis/custom.metrics.k8s.io/v1beta1/namespaces/procs/services/proc-metrics/queue-size

[$blank, $apis, $customMetrics, $v1Beta1, $namespaces, $namespace, $services, $service, $metric] = $parts;

if ($metric != "queue-size") {
	echo "I only know about queue-size currently";
	exit();
}
if ($namespace != "procs") {
	echo "I only know about procs namespace currently";
	exit();
}
if ($service != "job-scheduler-hal-proc") {
	echo "I only know about job-scheduler-hal-proc service currently";
	exit();
}

// TODO proxy some service to get an actual value to put in the response?
$data = [
	"kind" => "MetricValueList",
	"apiVersion" => "custom.metrics.k8s.io/v1beta1",
	"metadata" => [
		"selfLink" => "/apis/custom.metrics.k8s.io/v1beta1/"
	],
	"items" => [
		[
			"describedObject" => [
				"kind" => "Service",
				"namespace" => $namespace,
				"name" => $service,
				"apiVersion" => "/v1beta1"
			],
			"metricName" => $metric,
			"timestamp" => date("c"),
			"value" => 10
		]
	]
];
echo json_encode($data, JSON_PRETTY_PRINT);

