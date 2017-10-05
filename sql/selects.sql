SELECT areas.id, areas.name, hierarchy_areas_areas.id_up FROM areas, hierarchy_areas_areas WHERE areas.id = hierarchy_areas_areas.id

/*
id	name			id_up
1	MISAU			0
2	Niassa			1
3	Cabo Delgado	1
4	Nampula			1
5	Tete			1
6	Zambezia		1
...
17	Majune			2
18	Mandimba		2
...
*/
========================================================================

SELECT unit_code.code, units.name FROM unit_code, units WHERE unit_code.id_unit = units.id ORDER BY unit_code.date

/*
code	name
1020100	Hospital Provincial de Pemba HP
1020106	B. Cariacó CS III
1020107	B. Ingonane CSURB
1020108	Natite CSURB
1020109	B. Cimento CSURB
1020110	B. Eduardo Mondlane PS
1020111	Mahate CSURB
1020112	B. Muxara PS
1020113	Centro de Saude de Paquite
1020151	Chuiba PSA
1020206	Ancuabe CS I
1020207	Mariri PS
1020209	Metoro CS III
1020210	Meza CS III
1020211	Minhewene PS
1020212	Ntutupue CS II
1020213	Ngewe PS
1020214	Nacuale CS II
1020306	Balama CS I
1020307	Impiri CS III
1020308	Kuekué CS III
1020309	Mavala PS
1020310	Ntete PS
1020311	Metata PS
1020312	Murripa CS II
*/
========================================================================

SELECT unit_code.code, units.name, units_coord.lat, units_coord.lon
	FROM unit_code, units, units_coord
	WHERE unit_code.id_unit = units.id AND units_coord.id_unit = units.id
	ORDER BY unit_code.date

/*
name							lat			lon			code
Hospital Provincial de Pemba HP	-12.9658	40.4953		1020100
B. Cariacó CS III				-12.9631	40.5172		1020106
B. Ingonane CSURB				-12.9532	40.501		1020107
Natite CSURB					-12.9619	40.5093		1020108
B. Cimento CSURB				-12.9635	40.4933		1020109
B. Eduardo Mondlane PS			-12.9711	40.5533		1020110
Mahate CSURB					-13.0192	40.5293		1020111
B. Muxara PS					-13.0597	40.525		1020112
Centro de Saude de Paquite		-12.9625	40.4867		1020113
Chuiba PSA						-13.0225	40.5656		1020151
Ancuabe CS I					-12.9672	39.8572		1020206
Mariri PS						-13.0917	39.5931		1020207
Metoro CS III					-13.105		39.8742		1020209
Meza CS III						-13.0319	39.5522		1020210
Minhewene PS					-12.9939	39.4675		1020211
Ntutupue CS II					-13.1398	40.0563		1020212
Ngewe PS						-12.8603	39.9492		1020213
Nacuale CS II					-12.9642	39.6942		1020214
Balama CS I						-13.3482	38.5669		1020306
Impiri CS III					-13.3778	38.3703		1020307
Kuekué CS III					-13.5469	38.4244		1020308
Mavala PS						-13.2261	38.4806		1020309
Ntete PS						-13.4286	38.5947		1020310
Metata PS						-13.2733	38.6361		1020311
Murripa CS II					-13.4613	38.6908		1020312
*/
========================================================================

SELECT unit_code.code AS 'Id MISAU',
		units.name AS 'Nome da unidade',
		units_coord.lat AS Latitude, 
		units_coord.lon AS Longitude
	FROM unit_code, units, units_coord
	WHERE unit_code.id_unit = units.id AND units_coord.id_unit = units.id
	ORDER BY unit_code.date

/*
Id MISAU	Nome da unidade						Latitude	Longitude
1020100		Hospital Provincial de Pemba HP		-12.9658	40.4953
...
*/
========================================================================

SELECT unit_code.code AS 'Id MISAU',
		units.name AS 'Nome da unidade',
		hierarchy_units_areas.id_area AS 'Código do distrito', 
		units_coord.lat AS Latitude, 
		units_coord.lon AS Longitude
	FROM unit_code, units, hierarchy_units_areas, units_coord
	WHERE unit_code.id_unit = units.id
		AND units_coord.id_unit = units.id
		AND hierarchy_units_areas.id_unit = units.id
	ORDER BY unit_code.date

/*
Id MISAU	Nome da unidade					Código do distrito	Latitude	Longitude
1020100		Hospital Provincial de Pemba HP	46					-12.9658	40.4953
1020106		B. Cariacó CS III				46					-12.9631	40.5172
1020107		B. Ingonane CSURB				46					-12.9532	40.501
1020108		Natite CSURB					46					-12.9619	40.5093
1020109		B. Cimento CSURB				46					-12.9635	40.4933
1020110		B. Eduardo Mondlane PS			46					-12.9711	40.5533
1020111		Mahate CSURB					46					-13.0192	40.5293
1020112		B. Muxara PS					46					-13.0597	40.525
1020113		Centro de Saude de Paquite		46					-12.9625	40.4867
1020151		Chuiba PSA						46					-13.0225	40.5656
1020206		Ancuabe CS I					31					-12.9672	39.8572
1020207		Mariri PS						31					-13.0917	39.5931
1020209		Metoro CS III					31					-13.105		39.8742
1020210		Meza CS III						31					-13.0319	39.5522
1020211		Minhewene PS					31					-12.9939	39.4675
1020212		Ntutupue CS II					31					-13.1398	40.0563
1020213		Ngewe PS						31					-12.8603	39.9492
1020214		Nacuale CS II					31					-12.9642	39.6942
1020306		Balama CS I						32					-13.3482	38.5669
1020307		Impiri CS III					32					-13.3778	38.37
*/
========================================================================

SELECT unit_code.code AS "Id MISAU",
		units.name AS "Nome da unidade",
		hierarchy_units_areas.id_area AS "Id dis",
		areas.name AS Distrito,
		units_coord.lat AS Latitude, 
		units_coord.lon AS Longitude
	FROM unit_code, units, hierarchy_units_areas, units_coord, areas
	WHERE unit_code.id_unit = units.id
		AND units_coord.id_unit = units.id
		AND hierarchy_units_areas.id_unit = units.id
		AND areas.id = hierarchy_units_areas.id_area
	ORDER BY unit_code.date

/*
Id MISAU	Nome da unidade						Id dis	Distrito	Latitude	Longitude
1020100		Hospital Provincial de Pemba HP		46		Pemba		-12.9658	40.4953
1020106		B. Cariacó CS III					46		Pemba		-12.9631	40.5172
1020107		B. Ingonane CSURB					46		Pemba		-12.9532	40.501
1020108		Natite CSURB						46		Pemba		-12.9619	40.5093
1020109		B. Cimento CSURB					46		Pemba		-12.9635	40.4933
1020110		B. Eduardo Mondlane PS				46		Pemba		-12.9711	40.5533
1020111		Mahate CSURB						46		Pemba		-13.0192	40.5293
1020112		B. Muxara PS						46		Pemba		-13.0597	40.525
1020113		Centro de Saude de Paquite			46		Pemba		-12.9625	40.4867
1020151		Chuiba PSA							46		Pemba		-13.0225	40.5656
1020206		Ancuabe CS I						31		Ancuabe		-12.9672	39.8572
1020207		Mariri PS							31		Ancuabe		-13.0917	39.5931
1020209		Metoro CS III						31		Ancuabe		-13.105		39.8742
1020210		Meza CS III							31		Ancuabe		-13.0319	39.5522
1020211		Minhewene PS						31		Ancuabe		-12.9939	39.4675
1020212		Ntutupue CS II						31		Ancuabe		-13.1398	40.0563
1020213		Ngewe PS							31		Ancuabe		-12.8603	39.9492
1020214		Nacuale CS II						31		Ancuabe		-12.9642	39.6942
1020306		Balama CS I							32		Balama		-13.3482	38.5669
1020307		Impiri CS III						32		Balama		-13.3778	38.3703
1020308		Kuekué CS III						32		Balama		-13.5469	38.4244
1020309		Mavala PS							32		Balama		-13.2261	38.4806
1020310		Ntete PS							32		Balama		-13.4286	38.5947
1020311		Metata PS							32		Balama		-13.2733	38.6361
*/
========================================================================
SELECT unit_code.code AS "Id MISAU",
		units.name AS "Nome da unidade",
		hierarchy_units_areas.id_area AS Id_dis,
		areas.name AS Distrito,
		(SELECT hierarchy_areas_areas.id_up FROM hierarchy_areas_areas WHERE hierarchy_areas_areas.id = Id_dis) AS id_prov, 
		units_coord.lat AS Latitude, 
		units_coord.lon AS Longitude
	FROM unit_code, units, hierarchy_units_areas, units_coord, areas
	WHERE unit_code.id_unit = units.id
		AND units_coord.id_unit = units.id
		AND hierarchy_units_areas.id_unit = units.id
		AND areas.id = hierarchy_units_areas.id_area
	ORDER BY unit_code.date

/*
Id MISAU 	Nome da unidade 				Id_dis 	Distrito 	id_prov 	Latitude 	Longitude 
1020100		Hospital Provincial de Pemba HP	46		Pemba				3	-12.9658	40.4953
1020106		B. Cariacó CS III				46		Pemba				3	-12.9631	40.5172
1020107		B. Ingonane CSURB				46		Pemba				3	-12.9532	40.501
1020108		Natite CSURB					46		Pemba				3	-12.9619	40.5093
1020109		B. Cimento CSURB				46		Pemba				3	-12.9635	40.4933
1020110		B. Eduardo Mondlane PS			46		Pemba				3	-12.9711	40.5533
1020111		Mahate CSURB					46		Pemba				3	-13.0192	40.5293
1020112		B. Muxara PS					46		Pemba				3	-13.0597	40.525
1020113		Centro de Saude de Paquite		46		Pemba				3	-12.9625	40.4867
1020151		Chuiba PSA						46		Pemba				3	-13.0225	40.5656
1020206		Ancuabe CS I					31		Ancuabe				3	-12.9672	39.8572
1020207		Mariri PS						31		Ancuabe				3	-13.0917	39.5931
1020209		Metoro CS III					31		Ancuabe				3	-13.105	39.8742
1020210		Meza CS III						31		Ancuabe				3	-13.0319	39.5522
*/
========================================================================
SELECT 	units.id AS n,
		unit_code.code AS "Id MISAU",
		units.name AS "Nome da unidade",
		(SELECT unit_unit_type.id_type FROM unit_unit_type WHERE unit_unit_type.id_unit = n) AS id_tipo,
		(SELECT unit_type.name FROM unit_type WHERE unit_type.id = id_tipo) AS tipo,
		hierarchy_units_areas.id_area AS id_dis,
		areas.name AS Distrito,
		(SELECT hierarchy_areas_areas.id_up FROM hierarchy_areas_areas WHERE hierarchy_areas_areas.id = id_dis) AS id_prov, 
		(SELECT areas.name FROM areas WHERE areas.id = id_prov) AS Província,
		units_coord.lat AS Latitude, 
		units_coord.lon AS Longitude
	FROM unit_code, units, hierarchy_units_areas, units_coord, areas
	WHERE unit_code.id_unit = units.id
		AND units_coord.id_unit = units.id
		AND hierarchy_units_areas.id_unit = units.id
		AND areas.id = hierarchy_units_areas.id_area
	ORDER BY unit_code.date

/*
n  	Id MISAU  	Nome da unidade  				id_tipo  	tipo  				id_dis  	Distrito  	id_prov  	Província  	Latitude  	Longitude  
1	1020100		Hospital Provincial de Pemba HP	10			Hospital Provincial		46		Pemba		3		Cabo Delgado	-12.9658	40.4953
2	1020106		B. Cariacó CS III				2			Centro de Saúde Rural I	46		Pemba		3		Cabo Delgado	-12.9631	40.5172
3	1020107		B. Ingonane CSURB				2			Centro de Saúde Rural I	46		Pemba		3		Cabo Delgado	-12.9532	40.501
4	1020108		Natite CSURB					2			Centro de Saúde Rural I	46		Pemba		3		Cabo Delgado	-12.9619	40.5093
5	1020109		B. Cimento CSURB				2			Centro de Saúde Rural I	46		Pemba		3		Cabo Delgado	-12.9635	40.4933
6	1020110		B. Eduardo Mondlane PS			2			Centro de Saúde Rural I	46		Pemba		3		Cabo Delgado	-12.9711	40.5533
7	1020111		Mahate CSURB					2			Centro de Saúde Rural I	46		Pemba		3		Cabo Delgado	-13.0192	40.5293
8	1020112		B. Muxara PS					2			Centro de Saúde Rural I	46		Pemba		3		Cabo Delgado	-13.0597	40.525
9	1020113		Centro de Saude de Paquite		2			Centro de Saúde Rural I	46		Pemba		3		Cabo Delgado	-12.9625	40.4867
10	1020151		Chuiba PSA						2			Centro de Saúde Rural I	46		Pemba		3		Cabo Delgado	-13.0225	40.5656
11	1020206		Ancuabe CS I					2			Centro de Saúde Rural I	31		Ancuabe		3		Cabo Delgado	-12.9672	39.8572
12	1020207		Mariri PS						2			Centro de Saúde Rural I	31		Ancuabe		3		Cabo Delgado	-13.0917	39.5931
13	1020209		Metoro CS III					2			Centro de Saúde Rural I	31		Ancuabe		3		Cabo Delgado	-13.105		39.8742
*/

========================================================================
SELECT
	areas.id AS id,
	areas.name AS name,
	CAST(hierarchy_areas_areas.id_up AS UNSIGNED) AS id_up,
	CONCAT("a_",areas.id) AS sid,
	3 as v
FROM areas, hierarchy_areas_areas
WHERE areas.id = hierarchy_areas_areas.id AND hierarchy_areas_areas.id_up < 13 

UNION SELECT
	(@cnt := @cnt + 1) AS id, 
	units.name AS name, 
	CAST(hierarchy_units_areas.id_area AS UNSIGNED) AS id_up,
	CONCAT("u_",units.id) AS sid,
	units.valid as v
FROM units, hierarchy_units_areas

CROSS JOIN (SELECT @cnt := 10000) AS dummy 
WHERE units.id = hierarchy_units_areas.id_unit
ORDER BY id_up, id

/*
id 	name 			id_up 	sid 	v 	
1 	MISAU 			0 		a_1 	3
2 	Niassa 			1 		a_2 	3
3 	Cabo Delgado 	1 		a_3 	3
4 	Nampula 		1 		a_4 	3
5 	Tete 			1 		a_5 	3
6 	Zambezia 		1 		a_6 	3
7 	Manica 			1 		a_7 	3
8 	Sofala 			1 		a_8 	3
9 	Gaza 			1 		a_9 	3
10 	Inhambane 		1 		a_10 	3
11 	Maputo cidade 	1 		a_11 	3
12 	Maputo 			1 		a_12 	3
13 	Chimbonila 		2 		a_13 	3
14 	Cuamba 			2 		a_14 	3
15 	Lago 			2 		a_15 	3
*/

========================================================================


========================================================================


